<?php 
/**
 * Staff Management Page
 * Displays a list of all staff members and provides options to edit or delete them.
 */

require_once '../db/db_conn.php';
require_once '../includes/header.php'; 

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";
$status = "";

// Handle Delete Action
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Prevent self-deletion for security
    if ($id == $_SESSION['user_id']) {
        $message = "You cannot delete your own account.";
        $status = "warning";
    } else {
        $stmt = $conn->prepare("DELETE FROM user WHERE user_id = ?");
        $stmt->bind_param("s", $id);
        
        if ($stmt->execute()) {
            $message = "Staff member deleted successfully!";
            $status = "success";
        } else {
            $message = "Error deleting staff member: " . $conn->error;
            $status = "danger";
        }
        $stmt->close();
    }
}

// Fetch all staff members
$result = $conn->query("SELECT * FROM user ORDER BY first_name ASC");
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Navigation -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main Content Area -->
        <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Staff Management</h1>
                <a href="../auth/register.php" class="btn btn-primary shadow-sm">
                    <i class="bi bi-person-plus"></i> Add New Staff
                </a>
            </div>

            <!-- Feedback Messages -->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $status; ?> alert-dismissible fade show shadow-sm">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Staff Table Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3">User ID</th>
                                    <th class="py-3">Full Name</th>
                                    <th class="py-3">Username</th>
                                    <th class="py-3">Email</th>
                                    <th class="px-4 py-3 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr class="align-middle">
                                            <td class="px-4"><?php echo htmlspecialchars($row['user_id']); ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                                            <td class="px-4 text-end">
                                                <div class="btn-group">
                                                    <a href="edit.php?id=<?php echo $row['user_id']; ?>" 
                                                       class="btn btn-sm btn-outline-warning">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                    <a href="index.php?delete=<?php echo $row['user_id']; ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this staff member?')">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            No staff members found.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
