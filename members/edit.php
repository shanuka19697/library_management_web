<?php 
include '../db/db_conn.php';
include '../includes/header.php'; 

if (!isset($_SESSION['user_id'])) { header("Location: ../auth/login.php"); exit(); }

$id = $_GET['id'] ?? '';
$message = ""; $status = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $birthday = $_POST['birthday'] ?? '';
    $email = $_POST['email'] ?? '';

    $stmt = $conn->prepare("UPDATE member SET first_name = ?, last_name = ?, birthday = ?, email = ? WHERE member_id = ?");
    $stmt->bind_param("sssss", $first_name, $last_name, $birthday, $email, $id);
    if ($stmt->execute()) { $message = "Updated!"; $status = "success"; }
    else { $message = "Error: " . $conn->error; $status = "danger"; }
    $stmt->close();
}

$stmt = $conn->prepare("SELECT * FROM member WHERE member_id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<div class="container mt-5"><div class="row justify-content-center"><div class="col-md-6"><div class="card p-4">
    <h3>Edit Member</h3>
    <?php if ($message): ?><div class="alert alert-<?php echo $status; ?>"><?php echo $message; ?></div><?php endif; ?>
    <form method="POST">
        <div class="mb-3"><label class="form-label">Member ID</label><input type="text" class="form-control" value="<?php echo $member['member_id']; ?>" readonly></div>
        <div class="mb-3"><label class="form-label">First Name</label><input type="text" class="form-control" name="first_name" value="<?php echo $member['first_name']; ?>" required></div>
        <div class="mb-3"><label class="form-label">Last Name</label><input type="text" class="form-control" name="last_name" value="<?php echo $member['last_name']; ?>" required></div>
        <div class="mb-3"><label class="form-label">Birthday</label><input type="date" class="form-control" name="birthday" value="<?php echo $member['birthday']; ?>" required></div>
        <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?php echo $member['email']; ?>" required></div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </form>
</div></div></div></div>
<?php include '../includes/footer.php'; ?>
