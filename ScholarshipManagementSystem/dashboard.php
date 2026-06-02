<?php
require_once 'auth_check.php';

// Statistics
$total_applicants = $conn->query("SELECT COUNT(*) FROM scholarship_applications")->fetchColumn();
$approved = $conn->query("SELECT COUNT(*) FROM scholarship_applications WHERE application_status = 'Approved'")->fetchColumn();
$pending = $conn->query("SELECT COUNT(*) FROM scholarship_applications WHERE application_status = 'Pending'")->fetchColumn();
$requirements_complete = $conn->query("SELECT COUNT(*) FROM scholarship_applications WHERE requirements_complete = 1")->fetchColumn();
$requirements_incomplete = $conn->query("SELECT COUNT(*) FROM scholarship_applications WHERE requirements_complete = 0")->fetchColumn();

// Get current user's role
$user_role = $_SESSION['role'] ?? 'applicant';
$user_id = $_SESSION['user_id'];

// If applicant, only show their own applications
if ($user_role == 'applicant') {
    $recent_applications = $conn->query("SELECT * FROM scholarship_applications ORDER BY created_at DESC LIMIT 5")->fetchAll();
} else {
    // Admin sees all
    $recent_applications = $conn->query("SELECT * FROM scholarship_applications ORDER BY created_at DESC LIMIT 5")->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Scholarship Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-blue">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-graduation-cap"></i> SMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                    </li>
                    <?php if ($user_role == 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="create.php"><i class="fas fa-plus"></i> Add Applicant</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="read.php"><i class="fas fa-table"></i> 
                            <?php echo $user_role == 'applicant' ? 'My Applications' : 'All Applicants'; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="welcome-section">
            <h1><i class="fas fa-hand-sparkles"></i> Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p class="text-grey">
                <?php if ($user_role == 'admin'): ?>
                    Scholarship Admin Dashboard - Manage all scholarship applications
                <?php else: ?>
                    Scholarship Applicant Dashboard - Manage your scholarship application
                <?php endif; ?>
            </p>
        </div>
        
        <?php if ($user_role == 'admin'): ?>
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-primary"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <h3><?php echo $total_applicants; ?></h3>
                        <p>Total Applicants</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-success"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-info">
                        <h3><?php echo $approved; ?></h3>
                        <p>Approved</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-warning"><i class="fas fa-clock"></i></div>
                    <div class="stat-info">
                        <h3><?php echo $pending; ?></h3>
                        <p>Pending</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-info"><i class="fas fa-file-alt"></i></div>
                    <div class="stat-info">
                        <h3><?php echo $requirements_complete; ?></h3>
                        <p>Requirements Complete</p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="content-card">
                    <h4>
                        <i class="fas fa-clock"></i> 
                        <?php echo $user_role == 'applicant' ? 'My Applications' : 'Recent Applications'; ?>
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <?php if ($user_role == 'admin'): ?>
                                    <th>ID</th>
                                    <?php endif; ?>
                                    <th>Applicant</th>
                                    <th>University</th>
                                    <th>GWA</th>
                                    <th>Status</th>
                                    <th>Requirements</th>
                                    <?php if ($user_role == 'admin'): ?>
                                    <th>Action</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_applications as $app): ?>
                                <tr>
                                    <?php if ($user_role == 'admin'): ?>
                                    <td><?php echo $app['id']; ?></td>
                                    <?php endif; ?>
                                    <td><?php echo htmlspecialchars($app['applicant_name']); ?></td>
                                    <td><?php echo htmlspecialchars($app['university']); ?></td>
                                    <td><strong><?php echo number_format($app['gwa'], 2); ?></strong></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $app['application_status'] == 'Approved' ? 'success' : 
                                                ($app['application_status'] == 'Pending' ? 'warning' : 'secondary');
                                        ?>">
                                            <?php echo htmlspecialchars($app['application_status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($app['requirements_complete']): ?>
                                            <span class="badge bg-success"><i class="fas fa-check"></i> Complete</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="fas fa-times"></i> Incomplete</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($user_role == 'admin'): ?>
                                    <td>
                                        <a href="edit.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>