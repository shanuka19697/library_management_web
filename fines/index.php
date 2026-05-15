<?php
include '../db/db_conn.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";
$status = "";

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM fine WHERE fine_id = ?");
    $stmt->bind_param("s", $id);
    if ($stmt->execute()) {
        $message = "Fine deleted!";
        $status = "success";
    } else {
        $message = "Delete failed.";
        $status = "danger";
    }
    $stmt->close();
}

$result = $conn->query("SELECT f.*, m.first_name, m.last_name, b.book_name FROM fine f 
                        JOIN member m ON f.member_id = m.member_id 
                        JOIN book b ON f.book_id = b.book_id");
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>

        <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Fines Management</h1>
                <a href="add.php" class="btn btn-primary">Assign Fine</a>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $status; ?> alert-dismissible fade show"><?php echo $message; ?><button
                        type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <div class="card p-4">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Member ID</th>
                            <th>Member Name</th>
                            <th>Book</th>
                            <th>Amount (LKR)</th>
                            <th>Modified</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['fine_id']; ?></td>
                                <td><?php echo $row['member_id']; ?></td>
                                <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                                <td><?php echo $row['book_name']; ?></td>
                                <td><?php echo $row['fine_amount']; ?></td>
                                <td><?php echo $row['fine_date_modified']; ?></td>
                                <td>
                                    <a href="edit.php?id=<?php echo $row['fine_id']; ?>"
                                        class="btn btn-sm btn-warning">Edit</a>
                                    <a href="index.php?delete=<?php echo $row['fine_id']; ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure?')">Delete</a>
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