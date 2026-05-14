<?php 
include '../db/db_conn.php';
include '../includes/header.php'; 

if (!isset($_SESSION['user_id'])) { header("Location: ../auth/login.php"); exit(); }

$message = ""; $status = "";

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM bookborrower WHERE borrow_id = ?");
    $stmt->bind_param("s", $id);
    if ($stmt->execute()) { $message = "Record deleted!"; $status = "success"; }
    else { $message = "Delete failed."; $status = "danger"; }
    $stmt->close();
}

$result = $conn->query("SELECT bb.*, b.book_name, m.first_name FROM bookborrower bb 
                        JOIN book b ON bb.book_id = b.book_id 
                        JOIN member m ON bb.member_id = m.member_id");
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>

        <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Book Borrowing</h1>
                <a href="add.php" class="btn btn-primary">Borrow Book</a>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $status; ?> alert-dismissible fade show"><?php echo $message; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <div class="card p-4">
                <table class="table table-hover">
                    <thead>
                        <tr><th>ID</th><th>Book</th><th>Member</th><th>Status</th><th>Modified</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['borrow_id']; ?></td>
                            <td><?php echo $row['book_name']; ?></td>
                            <td><?php echo $row['first_name']; ?></td>
                            <td><span class="badge <?php echo $row['borrow_status'] == 'borrowed' ? 'bg-danger' : 'bg-success'; ?>"><?php echo $row['borrow_status']; ?></span></td>
                            <td><?php echo $row['borrower_date_modified']; ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $row['borrow_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="index.php?delete=<?php echo $row['borrow_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
