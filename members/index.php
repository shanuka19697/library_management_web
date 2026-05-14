<?php 
include '../db/db_conn.php';
include '../includes/header.php'; 

if (!isset($_SESSION['user_id'])) { header("Location: ../auth/login.php"); exit(); }

$message = ""; $status = "";

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM member WHERE member_id = ?");
    $stmt->bind_param("s", $id);
    if ($stmt->execute()) { $message = "Member deleted!"; $status = "success"; }
    else { $message = "Delete failed."; $status = "danger"; }
    $stmt->close();
}

$result = $conn->query("SELECT * FROM member");
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>

        <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Library Members</h1>
                <a href="add.php" class="btn btn-primary">Add New Member</a>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $status; ?> alert-dismissible fade show"><?php echo $message; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <div class="card p-4">
                <table class="table table-hover">
                    <thead>
                        <tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Birthday</th><th>Email</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['member_id']; ?></td>
                            <td><?php echo $row['first_name']; ?></td>
                            <td><?php echo $row['last_name']; ?></td>
                            <td><?php echo $row['birthday']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $row['member_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="index.php?delete=<?php echo $row['member_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
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
