<?php
require_once 'auth_check.php';
require_once 'config.php';

// Only admin can add applicants
if ($_SESSION['role'] != 'admin') {
    header('Location: read.php');
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $applicant_name = trim($_POST['applicant_name']);
    $applicant_phone = trim($_POST['applicant_phone']);
    $gwa = $_POST['gwa'];
    $grade_level = trim($_POST['grade_level']);
    $university = trim($_POST['university']);
    $year_level = $_POST['year_level'];
    $address = trim($_POST['address']);
    $parent_guardian_name = trim($_POST['parent_guardian_name']);
    $parent_guardian_contact = trim($_POST['parent_guardian_contact']);
    
    // Handle file uploads
    $certificate_of_grades_file = null;
    $certificate_of_registration_file = null;
    $certificate_of_grades_uploaded = 0;
    $certificate_of_registration_uploaded = 0;
    
    // Upload Certificate of Grades
    if (isset($_FILES['certificate_of_grades_file']) && $_FILES['certificate_of_grades_file']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['certificate_of_grades_file'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (in_array($extension, ['pdf', 'jpg', 'jpeg', 'png']) && $file['size'] <= 5242880) {
            $filename = uniqid() . '_grades_' . time() . '.' . $extension;
            $filepath = UPLOAD_DIR . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                $certificate_of_grades_file = $filename;
                $certificate_of_grades_uploaded = 1;
            }
        }
    }
    
    // Upload Certificate of Registration
    if (isset($_FILES['certificate_of_registration_file']) && $_FILES['certificate_of_registration_file']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['certificate_of_registration_file'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (in_array($extension, ['pdf', 'jpg', 'jpeg', 'png']) && $file['size'] <= 5242880) {
            $filename = uniqid() . '_reg_' . time() . '.' . $extension;
            $filepath = UPLOAD_DIR . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                $certificate_of_registration_file = $filename;
                $certificate_of_registration_uploaded = 1;
            }
        }
    }
    
    $requirements_complete = ($certificate_of_grades_uploaded && $certificate_of_registration_uploaded) ? 1 : 0;
    $application_status = $requirements_complete ? 'Pending' : 'Requirements Incomplete';
    
    if (empty($applicant_name) || empty($gwa) || empty($university)) {
        $error = 'Please fill in all required fields';
    } elseif ($gwa < 0.00 || $gwa > 5.00) {
        $error = 'GWA must be between 0.00 and 5.00';
    } else {
        $stmt = $conn->prepare("INSERT INTO scholarship_applications 
            (applicant_name, applicant_phone, gwa, grade_level, university, year_level, address,
            application_status, requirements_complete, parent_guardian_name, parent_guardian_contact,
            certificate_of_grades_uploaded, certificate_of_registration_uploaded,
            certificate_of_grades_file, certificate_of_registration_file) VALUES 
            (:applicant_name, :applicant_phone, :gwa, :grade_level, :university, :year_level, :address,
            :application_status, :requirements_complete, :parent_guardian_name, :parent_guardian_contact,
            :certificate_of_grades_uploaded, :certificate_of_registration_uploaded,
            :certificate_of_grades_file, :certificate_of_registration_file)");
        
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
            'certificate_of_grades_uploaded' => $certificate_of_grades_uploaded,
            'certificate_of_registration_uploaded' => $certificate_of_registration_uploaded,
            'certificate_of_grades_file' => $certificate_of_grades_file,
            'certificate_of_registration_file' => $certificate_of_registration_file
        ])) {
            $success = 'Applicant added successfully!';
            $_POST = [];
        } else {
            $error = 'Failed to add applicant';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Applicant - Scholarship Management System</title>
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
                    <li class="nav-item"><a class="nav-link active"><i class="fas fa-plus"></i> Add Applicant</a></li>
                    <li class="nav-item"><a class="nav-link" href="read.php"><i class="fas fa-table"></i> All Applicants</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="content-card">
            <h4><i class="fas fa-user-plus"></i> Add New College Scholarship Applicant</h4>
            <p class="text-grey">This system is for college students only (1st-4th Year College)</p>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" enctype="multipart/form-data">
                <h5 class="text-blue mt-3"><i class="fas fa-user"></i> Applicant Information</h5>
                <hr>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Applicant Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="applicant_name" required value="<?php echo htmlspecialchars($_POST['applicant_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Applicant Phone Number</label>
                        <input type="text" class="form-control" name="applicant_phone" value="<?php echo htmlspecialchars($_POST['applicant_phone'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">GWA (0.00 Format) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" name="gwa" required min="0.00" max="5.00" value="<?php echo $_POST['gwa'] ?? ''; ?>" placeholder="e.g., 1.50, 2.00">
                        <small class="text-grey">Enter GWA in 0.00 format (0.00 - 5.00). Lower is better.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Grade Level/Year in College</label>
                        <input type="text" class="form-control" name="grade_level" value="<?php echo htmlspecialchars($_POST['grade_level'] ?? 'College Year 2'); ?>">
                        <small class="text-grey">e.g., College Year 2, Junior Year, Senior Year</small>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">University <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="university" required value="<?php echo htmlspecialchars($_POST['university'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Year Level (College) <span class="text-danger">*</span></label>
                        <select class="form-select" name="year_level" required>
                            <option value="">Select College Year Level</option>
                            <option value="1st Year College" <?php echo ($_POST['year_level'] ?? '') == '1st Year College' ? 'selected' : ''; ?>>1st Year College</option>
                            <option value="2nd Year College" <?php echo ($_POST['year_level'] ?? '') == '2nd Year College' ? 'selected' : ''; ?>>2nd Year College</option>
                            <option value="3rd Year College" <?php echo ($_POST['year_level'] ?? '') == '3rd Year College' ? 'selected' : ''; ?>>3rd Year College</option>
                            <option value="4th Year College" <?php echo ($_POST['year_level'] ?? '') == '4th Year College' ? 'selected' : ''; ?>>4th Year College</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                </div>
                
                <h5 class="text-blue mt-4"><i class="fas fa-user-shield"></i> Parent/Guardian Information</h5>
                <hr>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Parent/Guardian Name</label>
                        <input type="text" class="form-control" name="parent_guardian_name" value="<?php echo htmlspecialchars($_POST['parent_guardian_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Parent/Guardian Contact</label>
                        <input type="text" class="form-control" name="parent_guardian_contact" value="<?php echo htmlspecialchars($_POST['parent_guardian_contact'] ?? ''); ?>">
                    </div>
                </div>
                
                <h5 class="text-blue mt-4"><i class="fas fa-file-alt"></i> Certificate Requirements (Upload Files)</h5>
                <hr>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Certificate of Grades</label>
                        <input type="file" class="form-control" name="certificate_of_grades_file" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-grey">PDF, JPG, JPEG, or PNG (Max 5MB)</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Certificate of Registration/Enrollment</label>
                        <input type="file" class="form-control" name="certificate_of_registration_file" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-grey">PDF, JPG, JPEG, or PNG (Max 5MB)</small>
                    </div>
                </div>
                
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Applicant</button>
                    <a href="read.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>