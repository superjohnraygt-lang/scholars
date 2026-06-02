<?php
require_once 'auth_check.php';

$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$university_filter = $_GET['university'] ?? '';

// Get current user's role
$user_role = $_SESSION['role'] ?? 'applicant';

$sql = "SELECT * FROM scholarship_applications WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (applicant_name LIKE :search OR applicant_phone LIKE :search)";
    $params['search'] = "%$search%";
}

if ($status_filter) {
    $sql .= " AND application_status = :status";
    $params['status'] = $status_filter;
}

if ($university_filter) {
    $sql .= " AND university = :university";
    $params['university'] = $university_filter;
}

$sql .= " ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$applications = $stmt->fetchAll();

// Get unique universities
$universities = $conn->query("SELECT DISTINCT university FROM scholarship_applications")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $user_role == 'admin' ? 'All Applicants' : 'My Applications'; ?>
        - Scholarship Management System
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-blue">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php"><i class="fas fa-graduation-cap"></i> SMS</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                    </li>
                    <?php if ($user_role == 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="create.php"><i class="fas fa-plus"></i> Add Applicant</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link active" href="read.php">
                            <i class="fas fa-table"></i> 
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
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>
                    <i class="fas fa-table"></i> 
                    <?php echo $user_role == 'admin' ? 'All Scholarship Applicants' : 'My Scholarship Applications'; ?>
                </h4>
                <?php if ($user_role == 'admin'): ?>
                <a href="create.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Applicant</a>
                <?php endif; ?>
            </div>
            
            <?php if ($user_role == 'admin'): ?>
            <form method="GET" action="" class="mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="search" placeholder="Search by name or phone..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="status">
                            <option value="">All Status</option>
                            <option value="Pending" <?php echo $status_filter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Under Review" <?php echo $status_filter == 'Under Review' ? 'selected' : ''; ?>>Under Review</option>
                            <option value="Approved" <?php echo $status_filter == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="Rejected" <?php echo $status_filter == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                            <option value="Requirements Incomplete" <?php echo $status_filter == 'Requirements Incomplete' ? 'selected' : ''; ?>>Requirements Incomplete</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="university">
                            <option value="">All Universities</option>
                            <?php foreach ($universities as $uni): ?>
                                <option value="<?php echo $uni['university']; ?>" <?php echo $university_filter == $uni['university'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($uni['university']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Filter</button>
                    </div>
                </div>
            </form>
            <?php endif; ?>
            
            <?php if (count($applications) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Applicant Name</th>
                            <th>Applicant Phone Number</th>
                            <th>GWA</th>
                            <th>Grade Level</th>
                            <th>University</th>
                            <th>Year Level</th>
                            <th>Address</th>
                            <th>Application Status</th>
                            <th>Requirements Complete</th>
                            <th>Parent/Guardian</th>
                            <th>Last Updated/Completed</th>
                            <th>Certificate of Grades</th>
                            <th>Certificate of Registration</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><?php echo $app['id']; ?></td>
                            <td><?php echo htmlspecialchars($app['applicant_name']); ?></td>
                            <td><?php echo htmlspecialchars($app['applicant_phone']); ?></td>
                            <td><strong><?php echo number_format($app['gwa'], 2); ?></strong></td>
                            <td><?php echo htmlspecialchars($app['grade_level']); ?></td>
                            <td><?php echo htmlspecialchars($app['university']); ?></td>
                            <td><?php echo htmlspecialchars($app['year_level']); ?></td>
                            <td><?php echo htmlspecialchars(substr($app['address'], 0, 40)) . (strlen($app['address']) > 40 ? '...' : ''); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $app['application_status'] == 'Approved' ? 'success' : 
                                        ($app['application_status'] == 'Pending' ? 'warning' : 
                                        ($app['application_status'] == 'Rejected' ? 'danger' : 'secondary'));
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
                            <td>
                                <?php if ($app['parent_guardian_name']): ?>
                                    <strong><?php echo htmlspecialchars($app['parent_guardian_name']); ?></strong><br>
                                    <small class="text-grey"><?php echo htmlspecialchars($app['parent_guardian_contact']); ?></small>
                                <?php else: ?>
                                    <span class="text-grey">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($app['completed_at']): ?>
                                    <?php echo date('M d, Y H:i', strtotime($app['completed_at'])); ?>
                                <?php else: ?>
                                    <?php echo date('M d, Y H:i', strtotime($app['last_updated'])); ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($app['certificate_of_grades_uploaded']): ?>
                                    <span class="badge bg-success"><i class="fas fa-check"></i> ✓</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="fas fa-times"></i> ✗</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($app['certificate_of_registration_uploaded']): ?>
                                    <span class="badge bg-success"><i class="fas fa-check"></i> ✓</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="fas fa-times"></i> ✗</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user_role == 'admin'): ?>
                                <a href="edit.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this applicant?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php else: ?>
                                <a href="edit.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <?php echo $user_role == 'applicant' ? 'You have no applications yet. ' : 'No applicants found. '; ?>
                    <?php if ($user_role == 'applicant'): ?>
                        <a href="create.php">Apply for scholarship</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>