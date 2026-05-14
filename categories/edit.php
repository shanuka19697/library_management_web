<?php 
include '../db/db_conn.php';
include '../includes/header.php'; 

if (!isset($_SESSION['user_id'])) { header("Location: ../auth/login.php"); exit(); }

$id = $_GET['id'] ?? '';
$message = ""; $status = "";
date_default_timezone_set('Asia/Colombo');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = $_POST['category_name'] ?? '';
    $current_date = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("UPDATE bookcategory SET category_Name = ?, date_modified = ? WHERE category_id = ?");
    $stmt->bind_param("sss", $category_name, $current_date, $id);
    if ($stmt->execute()) { $message = "Updated!"; $status = "success"; }
    else { $message = "Error: " . $conn->error; $status = "danger"; }
    $stmt->close();
}

$stmt = $conn->prepare("SELECT * FROM bookcategory WHERE category_id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$cat = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<div class="container mt-5"><div class="row justify-content-center"><div class="col-md-6"><div class="card p-4">
    <h3>Edit Category</h3>
    <?php if ($message): ?><div class="alert alert-<?php echo $status; ?>"><?php echo $message; ?></div><?php endif; ?>
    <form method="POST">
        <div class="mb-3"><label class="form-label">Category ID</label><input type="text" class="form-control" value="<?php echo $cat['category_id']; ?>" readonly></div>
        <div class="mb-3"><label class="form-label">Category Name</label><input type="text" class="form-control" name="category_name" value="<?php echo $cat['category_Name']; ?>" required></div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </form>
</div></div></div></div>
<?php include '../includes/footer.php'; ?>
