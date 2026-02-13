<?php
require_once 'includes/header.php';

$msg = '';
$msgType = 'success';

// Fetch current about info
$about = $pdo->query("SELECT * FROM about WHERE id = 1")->fetch();

// Create cv directory if not exists
if (!file_exists('../assets/cv')) {
    mkdir('../assets/cv', 0777, true);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_about'])) {
    $title = trim($_POST['title']);
    $subtitle = trim($_POST['subtitle']);
    $description = trim($_POST['description']);
    $experience_years = trim($_POST['experience_years']);
    $projects_completed = trim($_POST['projects_completed']);
    $happy_clients = trim($_POST['happy_clients']);

    $image_path = $about['image_path'] ?? '';
    $cv_path = $about['cv_path'] ?? '';
    $success_msg = [];

    // Handle image upload
    if (isset($_FILES['about_image']) && $_FILES['about_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['about_image']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $newFilename = 'profile-' . time() . '.' . $ext;
            $uploadPath = '../assets/img/' . $newFilename;

            if (move_uploaded_file($_FILES['about_image']['tmp_name'], $uploadPath)) {
                // Delete old image if exists
                if (!empty($about['image_path']) && file_exists('../' . $about['image_path'])) {
                    unlink('../' . $about['image_path']);
                }
                $image_path = 'assets/img/' . $newFilename;
                $success_msg[] = "Image updated";
            }
        }
    }

    // Handle CV Upload
    if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] != UPLOAD_ERR_NO_FILE) {
        if ($_FILES['cv_file']['error'] == 0) {
            $allowedVal = ['pdf', 'doc', 'docx'];
            $extVal = strtolower(pathinfo($_FILES['cv_file']['name'], PATHINFO_EXTENSION));

            if (in_array($extVal, $allowedVal)) {
                $newCvName = 'resume-' . time() . '.' . $extVal;
                $uploadCvPath = '../assets/cv/' . $newCvName;

                if (move_uploaded_file($_FILES['cv_file']['tmp_name'], $uploadCvPath)) {
                    // Delete old CV if exists
                    if (!empty($about['cv_path']) && file_exists('../' . $about['cv_path'])) {
                        unlink('../' . $about['cv_path']);
                    }
                    $cv_path = 'assets/cv/' . $newCvName;
                    $success_msg[] = "CV updated successfully";
                } else {
                    $error_msg = "Failed to move uploaded CV file.";
                }
            } else {
                $error_msg = "Invalid CV file type. Allowed: PDF, DOC, DOCX.";
            }
        } else {
            $error_msg = "CV Upload Error. Code: " . $_FILES['cv_file']['error'];
        }
    }

    if (isset($error_msg)) {
        header("Location: about.php?error=" . urlencode($error_msg));
        exit;
    }

    $sql = "UPDATE about SET title=?, subtitle=?, description=?, experience_years=?, projects_completed=?, happy_clients=?, image_path=?, cv_path=? WHERE id=1";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$title, $subtitle, $description, $experience_years, $projects_completed, $happy_clients, $image_path, $cv_path])) {
        $success_msg[] = "Details updated successfully";
        header("Location: about.php?success=" . urlencode(implode(", ", $success_msg)));
        exit;
    } else {
        header("Location: about.php?error=" . urlencode("Database synchronization failed"));
        exit;
    }
}

if (isset($_GET['success'])) {
    $msg = $_GET['success'];
    $msgType = 'success';
} elseif (isset($_GET['error'])) {
    $msg = $_GET['error'];
    $msgType = 'error';
}
?>

<div style="margin-bottom: 35px;">
    <h1 style="font-size: 1.8rem;">About Section Management</h1>
    <p style="color: var(--text-muted); font-size: 0.9rem;">Manage About Me, Photo, and CV Download</p>
</div>

<?php if ($msg): ?>
    <div
        style="background: <?php echo $msgType == 'error' ? 'rgba(255, 71, 87, 0.1)' : 'rgba(0, 229, 255, 0.1)'; ?>; 
                border: 1px solid <?php echo $msgType == 'error' ? '#ff4757' : 'var(--primary)'; ?>; 
                color: <?php echo $msgType == 'error' ? '#ff4757' : 'var(--primary)'; ?>; 
                padding: 15px 20px; border-radius: 12px; margin-bottom: 35px; display: flex; align-items: center; gap: 10px;">
        <i class="fas <?php echo $msgType == 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'; ?>"></i>
        <?php echo $msg; ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="grid-2">
        <!-- Left Column: Image & CV & Stats -->
        <div>
            <!-- Profile Image -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-image" style="margin-right: 10px; color: var(--primary);"></i> Profile Image
                    </h3>
                </div>

                <div style="text-align: center; padding: 20px;">
                    <div
                        style="width: 250px; height: 280px; margin: 0 auto 25px; border-radius: 20px; overflow: hidden; border: 3px solid var(--border); background: var(--card-bg);">
                        <img id="preview-image"
                            src="../<?php echo !empty($about['image_path']) ? htmlspecialchars($about['image_path']) : 'assets/img/about-img.jpg'; ?>?v=<?php echo time(); ?>"
                            alt="About Image Preview" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>

                    <label for="about_image" class="btn btn-secondary"
                        style="cursor: pointer; display: inline-flex; align-items: center; gap: 10px; padding: 12px 25px;">
                        <i class="fas fa-cloud-upload-alt"></i> Choose New Image
                    </label>
                    <input type="file" id="about_image" name="about_image" accept="image/*" style="display: none;"
                        onchange="previewImage(this)">
                </div>
            </div>

            <!-- CV Upload -->
            <div class="card" style="margin-top: 25px;">
                <div class="card-header">
                    <h3><i class="fas fa-file-pdf" style="margin-right: 10px; color: #ff4757;"></i> Resume / CV</h3>
                </div>
                <div style="padding: 15px;">
                    <p style="margin-bottom: 15px; color: var(--text-muted); font-size: 0.9rem;">
                        Upload your PDF resume. Visitors will download this file when they click "Download CV".
                    </p>

                    <?php if (!empty($about['cv_path'])): ?>
                        <div
                            style="margin-bottom: 15px; padding: 10px; background: rgba(0, 255, 0, 0.1); border-radius: 8px; font-size: 0.9rem;">
                            <i class="fas fa-check-circle" style="color: #2ecc71;"></i>
                            Current CV: <strong><?php echo basename($about['cv_path']); ?></strong>
                            (<a href="../download_cv.php" style="color: var(--primary);">Download</a>)
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Upload New CV (PDF)</label>
                        <input type="file" name="cv_file" accept=".pdf,.doc,.docx" class="form-control"
                            style="padding-top: 12px;">
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="card" style="margin-top: 25px;">
                <div class="card-header">
                    <h3><i class="fas fa-chart-bar" style="margin-right: 10px; color: var(--primary);"></i> Statistics
                    </h3>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                    <div class="form-group">
                        <label>Years Exp</label>
                        <input type="text" name="experience_years" class="form-control"
                            value="<?php echo htmlspecialchars($about['experience_years'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Projects</label>
                        <input type="text" name="projects_completed" class="form-control"
                            value="<?php echo htmlspecialchars($about['projects_completed'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Clients</label>
                        <input type="text" name="happy_clients" class="form-control"
                            value="<?php echo htmlspecialchars($about['happy_clients'] ?? ''); ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Content -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-file-alt" style="margin-right: 10px; color: var(--primary);"></i> About Content
                    </h3>
                </div>

                <div class="form-group">
                    <label>Section Title</label>
                    <input type="text" name="title" class="form-control"
                        value="<?php echo htmlspecialchars($about['title'] ?? ''); ?>"
                        placeholder="e.g. Turning Vision Into Reality">
                </div>

                <div class="form-group">
                    <label>Subtitle / Tagline</label>
                    <input type="text" name="subtitle" class="form-control"
                        value="<?php echo htmlspecialchars($about['subtitle'] ?? ''); ?>"
                        placeholder="e.g. Full Stack Web Developer">
                </div>

                <div class="form-group">
                    <label>About Description</label>
                    <textarea name="description" class="form-control" rows="10"
                        placeholder="Write about yourself..."><?php echo htmlspecialchars($about['description'] ?? ''); ?></textarea>
                </div>
            </div>

            <div
                style="margin-top: 25px; background: rgba(108, 92, 231, 0.05); border: 1px dashed var(--secondary); padding: 25px; border-radius: 20px; text-align: center;">
                <button type="submit" name="save_about" class="btn btn-primary" style="width: 100%; padding: 16px;">
                    <i class="fas fa-save"></i> Save All Changes
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('preview-image').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php require_once 'includes/footer.php'; ?>