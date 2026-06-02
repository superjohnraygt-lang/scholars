<?php
require_once 'auth_check.php';

$id = $_GET['id'] ?? 0;
$success = '';
$error = '';

$stmt = $conn->prepare("SELECT * FROM scholarship_applications WHERE id = :id");
$stmt->execute(['id' => $id]);
$application = $stmt->fetch();

if (!$application) {
    header('Location: read.php');
    exit();
}

// Check if applicant can edit (only their own or admin)
$user_role = $_SESSION['role'] ?? 'applicant';
if ($user_role == 'applicant') {
    // Applicants can only edit their own applications
    // For simplicity, allowing all applicants to edit in this demo
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $applicant_name = trim($_POST['applicant_name']);
    $applicant_phone = trim($_POST['applicant_phone']);
    $gwa = $_POST['gwa'];
    $grade_level = trim($_POST['grade_level']);
    $university = trim($_POST['university']);
    $year_level = $_POST['year_level'];
    $address = trim($_POST['address']);
    $application_status = $_POST['application_status'];
    $parent_guardian_name = trim($_POST['parent_guardian_name']);
    $parent_guardian_contact = trim($_POST['parent_guardian_contact']);
    $certificate_of_grades = isset($_POST['certificate_of_grades']) ? 1 : 0;
    $certificate_of_registration = isset($_POST['certificate_of_registration']) ? 1 : 0;
    
    $requirements_complete = ($certificate_of_grades && $certificate_of_registration) ? 1 : 0;
    
    // Validate GWA
    if ($gwa < 0.00 || $gwa > 5.00) {
        $error = 'GWA must be between 0.00 and 5.00';
    } else {
        $stmt = $conn->prepare("UPDATE scholarship_applications SET 
            applicant_name = :applicant_name, applicant_phone = :applicant_phone, gwa = :gwa,
            grade_level = :grade_level, university = :university, year_level = :year_level,
            address = :address, application_status = :application_status,
            requirements_complete = :requirements_complete, parent_guardian_name = :parent_guardian_name,
            parent_guardian_contact = :parent_guardian_contact,
            certificate_of_grades_uploaded = :certificate_of_grades,
            certificate_of_registration_uploaded = :certificate_of_registration WHERE id = :id");
        
        if ($stmt->execute([
            'applicant_name' => $applicant_name,
            'applicant_phone' => $applicant_phone,
            'gwa' => $gwa,
            'grade_level' => $grade_level,
            'university' => $university,
            'year_level' => $year_level,
            'address' => $address,
            'application_status' => $application_status,
            'requirements_complete' => $requirements_complete,
            'parent_guardian_name' => $parent_guardian_name,
            'parent_guardian_contact' => $parent_guardian_contact,
            'certificate_of_grades' => $certificate_of_grades,
            'certificate_of_registration' => $certificate_of_registration,
            'id' => $id
        ])) {
            $success = 'Applicant updated successfully!';
            $stmt = $conn->prepare("SELECT * FROM scholarship_applications WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $application = $stmt->fetch();
        } else {
            $error = 'Failed to update applicant';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Applicant - Scholarship Management System</title>
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
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="create.php"><i class="fas fa-plus"></i> Add Applicant</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link active" href="read.php"><i class="fas fa-table"></i> 
                        <?php echo $_SESSION['role'] == 'applicant' ? 'My Applications' : 'All Applicants'; ?>
                    </a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="content-card">
            <h4><i class="fas fa-edit"></i> Edit College Scholarship Applicant</h4>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Applicant Name *</label>
                        <input type="text" class="form-control" name="applicant_name" required value="<?php echo htmlspecialchars($application['applicant_name']); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Applicant Phone Number</label>
                        <input type="text" class="form-control" name="applicant_phone" value="<?php echo htmlspecialchars($application['applicant_phone']); ?>">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">GWA (0.00 Format) *</label>
                        <input type="number" step="0.01" class="form-control" name="gwa" required min="0.00" max="5.00" value="<?php echo number_format($application['gwa'], 2); ?>">
                        <small class="text-grey">Enter GWA in 0.00 format (0.00 - 5.00). Lower is better.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Grade Level/Year in College</label>
                        <input type="text" class="form-control" name="grade_level" value="<?php echo htmlspecialchars($application['grade_level']); ?>">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">University *</label>
                        <input type="text" class="form-control" name="university" required value="<?php echo htmlspecialchars($application['university']); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Year Level (College) *</label>
                        <select class="form-select" name="year_level" required>
                            <option value="1st Year College" <?php echo $application['year_level'] == '1st Year College' ? 'selected' : ''; ?>>1st Year College</option>
                            <option value="2nd Year College" <?php echo $application['year_level'] == '2nd Year College' ? 'selected' : ''; ?>>2nd Year College</option>
                            <option value="3rd Year College" <?php echo $application['year_level'] == '3rd Year College' ? 'selected' : ''; ?>>3rd Year College</option>
                            <option value="4th Year College" <?php echo $application['year_level'] == '4th Year College' ? 'selected' : ''; ?>>4th Year College</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars($application['address']); ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Application Status</label>
                    <select class="form-select" name="application_status">
                        <option value="Pending" <?php echo $application['application_status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Under Review" <?php echo $application['application_status'] == 'Under Review' ? 'selected' : ''; ?>>Under Review</option>
                        <option value="Approved" <?php echo $application['application_status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="Rejected" <?php echo $application['application_status'] == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                        <option value="Requirements Incomplete" <?php echo $application['application_status'] == 'Requirements Incomplete' ? 'selected' : ''; ?>>Requirements Incomplete</option>
                    </select>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Parent/Guardian Name</label>
                        <input type="text" class="form-control" name="parent_guardian_name" value="<?php echo htmlspecialchars($application['parent_guardian_name']); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Parent/Guardian Contact</label>
                        <input type="text" class="form-control" name="parent_guardian_contact" value="<?php echo htmlspecialchars($application['parent_guardian_contact']); ?>">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="certificate_of_grades" name="certificate_of_grades" <?php echo $application['certificate_of_grades_uploaded'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="certificate_of_grades">Certificate of Grades</label>
                    </div>
                    <div class="col-md-6 mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="certificate_of_registration" name="certificate_of_registration" <?php echo $application['certificate_of_registration_uploaded'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="certificate_of_registration">Certificate of Registration/Enrollment</label>
                    </div>
                </div>
                
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                    <a href="read.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>