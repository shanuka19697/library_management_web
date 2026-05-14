<?php 
/**
 * Admin Dashboard
 * Provides an overview of the library system with quick stats and actions.
 */

require_once 'db/db_conn.php';
require_once 'includes/header.php'; 

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Fetch dashboard statistics
$totalBooks    = $conn->query("SELECT COUNT(*) as count FROM book")->fetch_assoc()['count'];
$totalMembers  = $conn->query("SELECT COUNT(*) as count FROM member")->fetch_assoc()['count'];
$activeBorrows = $conn->query("SELECT COUNT(*) as count FROM bookborrower WHERE borrow_status = 'borrowed'")->fetch_assoc()['count'];
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Navigation -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Dashboard Content -->
        <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard Overview</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <span class="text-muted">
                        Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                    </span>
                </div>
            </div>

            <!-- Statistic Cards -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-center shadow-sm border-0 p-4 mb-4">
                        <div class="card-body">
                            <h5 class="text-muted">Total Books</h5>
                            <h2 class="display-4 font-weight-bold text-primary"><?php echo number_format($totalBooks); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center shadow-sm border-0 p-4 mb-4">
                        <div class="card-body">
                            <h5 class="text-muted">Total Members</h5>
                            <h2 class="display-4 font-weight-bold text-success"><?php echo number_format($totalMembers); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center shadow-sm border-0 p-4 mb-4">
                        <div class="card-body">
                            <h5 class="text-muted">Active Borrows</h5>
                            <h2 class="display-4 font-weight-bold text-info"><?php echo number_format($activeBorrows); ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions Section -->
            <div class="mt-4 p-4 bg-light rounded shadow-sm">
                <h4 class="mb-3">Quick Actions</h4>
                <div class="d-flex flex-wrap gap-3">
                    <a href="books/add.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add New Book
                    </a>
                    <a href="members/add.php" class="btn btn-success">
                        <i class="bi bi-person-plus"></i> Register Member
                    </a>
                    <a href="borrow/add.php" class="btn btn-info text-white">
                        <i class="bi bi-book"></i> Borrow a Book
                    </a>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
