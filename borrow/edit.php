<?php 
include '../db/db_conn.php';
include '../includes/header.php'; 

if (!isset($_SESSION['user_id'])) { header("Location: ../auth/login.php"); exit(); }

$id = $_GET['id'] ?? '';
$message = ""; $status = "";
date_default_timezone_set('Asia/Colombo');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = $_POST['book_id'] ?? '';
    $member_id = $_POST['member_id'] ?? '';
    $borrow_status = $_POST['borrow_status'] ?? '';
    $current_date = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("UPDATE bookborrower SET book_id = ?, member_id = ?, borrow_status = ?, borrower_date_modified = ? WHERE borrow_id = ?");
    $stmt->bind_param("sssss", $book_id, $member_id, $borrow_status, $current_date, $id);
    if ($stmt->execute()) { $message = "Updated!"; $status = "success"; }
    else { $message = "Error: " . $conn->error; $status = "danger"; }
    $stmt->close();
}

$stmt = $conn->prepare("SELECT * FROM bookborrower WHERE borrow_id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$bb = $stmt->get_result()->fetch_assoc();
$stmt->close();

$books = $conn->query("SELECT * FROM book");
$members = $conn->query("SELECT * FROM member");
?>
<div class="container mt-5"><div class="row justify-content-center"><div class="col-md-6"><div class="card p-4">
    <h3>Edit Borrow Record</h3>
    <?php if ($message): ?><div class="alert alert-<?php echo $status; ?>"><?php echo $message; ?></div><?php endif; ?>
    <form method="POST">
        <div class="mb-3"><label class="form-label">Borrow ID</label><input type="text" class="form-control" value="<?php echo $bb['borrow_id']; ?>" readonly></div>
        <div class="mb-3"><label class="form-label">Book</label>
            <select class="form-select" name="book_id" required>
                <?php while ($b = $books->fetch_assoc()): ?>
                    <option value="<?php echo $b['book_id']; ?>" <?php echo ($b['book_id'] == $bb['book_id']) ? 'selected' : ''; ?>><?php echo $b['book_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3"><label class="form-label">Member</label>
            <select class="form-select" name="member_id" required>
                <?php while ($m = $members->fetch_assoc()): ?>
                    <option value="<?php echo $m['member_id']; ?>" <?php echo ($m['member_id'] == $bb['member_id']) ? 'selected' : ''; ?>><?php echo $m['first_name'] . ' ' . $m['last_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3"><label class="form-label">Status</label>
            <select class="form-select" name="borrow_status" required>
                <option value="borrowed" <?php echo ($bb['borrow_status'] == 'borrowed') ? 'selected' : ''; ?>>Borrowed</option>
                <option value="available" <?php echo ($bb['borrow_status'] == 'available') ? 'selected' : ''; ?>>Available</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </form>
</div></div></div></div>
<?php include '../includes/footer.php'; ?>
