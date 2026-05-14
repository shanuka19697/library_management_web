<?php 
include '../db/db_conn.php';
include '../includes/header.php'; 

if (!isset($_SESSION['user_id'])) { header("Location: ../auth/login.php"); exit(); }

$message = ""; $status = "";

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM book WHERE book_id = ?");
    $stmt->bind_param("s", $id);
    if ($stmt->execute()) { $message = "Book deleted!"; $status = "success"; }
    else { $message = "Delete failed."; $status = "danger"; }
    $stmt->close();
}

$result = $conn->query("SELECT b.*, c.category_Name FROM book b LEFT JOIN bookcategory c ON b.category_id = c.category_id");
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>

        <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Books Management</h1>
                <a href="add.php" class="btn btn-primary">Add New Book</a>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $status; ?> alert-dismissible fade show"><?php echo $message; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <div class="card p-4">
                <table class="table table-hover">
                    <thead>
                        <tr><th>ID</th><th>Name</th><th>Category</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['book_id']; ?></td>
                            <td><?php echo $row['book_name']; ?></td>
                            <td><?php echo $row['category_Name'] ?? 'N/A'; ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $row['book_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="index.php?delete=<?php echo $row['book_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
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
