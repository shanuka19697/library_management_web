<?php 
include '../db/db_conn.php';
include '../includes/header.php'; 

if (!isset($_SESSION['user_id'])) { header("Location: ../auth/login.php"); exit(); }

$message = ""; $status = "";
date_default_timezone_set('Asia/Colombo');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $borrow_id = $_POST['borrow_id'] ?? '';
    $book_id = $_POST['book_id'] ?? '';
    $member_id = $_POST['member_id'] ?? '';
    $borrow_status = $_POST['borrow_status'] ?? 'borrowed';
    $current_date = date('Y-m-d H:i:s');

    if (!preg_match('/^BR\d{3}$/', $borrow_id)) {
        $message = "Invalid Borrow ID format (e.g. BR001)";
        $status = "danger";
    } else {
        $stmt = $conn->prepare("INSERT INTO bookborrower (borrow_id, book_id, member_id, borrow_status, borrower_date_modified) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $borrow_id, $book_id, $member_id, $borrow_status, $current_date);
        if ($stmt->execute()) { header("Location: index.php"); exit(); }
        else { $message = "Error: " . $conn->error; $status = "danger"; }
        $stmt->close();
    }
}

$books = $conn->query("SELECT * FROM book");
$members = $conn->query("SELECT * FROM member");
?>
<div class="container mt-5"><div class="row justify-content-center"><div class="col-md-6"><div class="card p-4">
    <h3>Borrow Book</h3>
    <?php if ($message): ?><div class="alert alert-<?php echo $status; ?>"><?php echo $message; ?></div><?php endif; ?>
    <form method="POST">
        <div class="mb-3"><label class="form-label">Borrow ID (BR001)</label><input type="text" class="form-control" name="borrow_id" required></div>
        <div class="mb-3"><label class="form-label">Book</label>
            <select class="form-select" name="book_id" required>
                <option value="">Select Book</option>
                <?php while ($b = $books->fetch_assoc()): ?>
                    <option value="<?php echo $b['book_id']; ?>"><?php echo $b['book_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3"><label class="form-label">Member</label>
            <select class="form-select" name="member_id" required>
                <option value="">Select Member</option>
                <?php while ($m = $members->fetch_assoc()): ?>
                    <option value="<?php echo $m['member_id']; ?>"><?php echo $m['first_name'] . ' ' . $m['last_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3"><label class="form-label">Status</label>
            <select class="form-select" name="borrow_status" required>
                <option value="borrowed">Borrowed</option>
                <option value="available">Available</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </form>
</div></div></div></div>
<?php include '../includes/footer.php'; ?>
