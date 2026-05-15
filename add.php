<?php
include '../db/db_conn.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";
$status = "";
date_default_timezone_set('Asia/Colombo');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fine_id = $_POST['fine_id'] ?? '';
    $member_id = $_POST['member_id'] ?? '';
    $book_id = $_POST['book_id'] ?? '';
    $fine_amount = $_POST['fine_amount'] ?? 0;
    $current_date = date('Y-m-d H:i:s');

    if ($fine_amount < 2 || $fine_amount > 500) {
        $message = "Fine amount must be between 2 and 500 LKR.";
        $status = "danger";
    } else {
        $stmt = $conn->prepare("INSERT INTO fine (fine_id, member_id, book_id, fine_amount, fine_date_modified) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $fine_id, $member_id, $book_id, $fine_amount, $current_date);
        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            $message = "Error: " . $conn->error;
            $status = "danger";
        }
        $stmt->close();
    }
}

$books = $conn->query("SELECT * FROM book");
$members = $conn->query("SELECT * FROM member");
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <h3>Assign Fine</h3>
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $status; ?>"><?php echo $message; ?></div><?php endif; ?>
                <form method="POST">
                    <div class="mb-3"><label class="form-label">Fine ID</label><input type="text" class="form-control"
                            name="fine_id" required></div>
                    <div class="mb-3"><label class="form-label">Member</label>
                        <select class="form-select" name="member_id" required>
                            <option value="">Select Member</option>
                            <?php while ($m = $members->fetch_assoc()): ?>
                                <option value="<?php echo $m['member_id']; ?>">
                                    <?php echo $m['first_name'] . ' ' . $m['last_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Book</label>
                        <select class="form-select" name="book_id" required>
                            <option value="">Select Book</option>
                            <?php while ($b = $books->fetch_assoc()): ?>
                                <option value="<?php echo $b['book_id']; ?>"><?php echo $b['book_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Fine Amount (2 - 500 LKR)</label>
                        <input type="number" class="form-control" name="fine_amount" min="2" max="500" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Assign</button>
                    <a href="index.php" class="btn btn-secondary">Back</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>