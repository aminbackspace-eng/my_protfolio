<?php
require_once 'includes/db.php';

// Fetch CV path from DB
$stmt = $pdo->query("SELECT cv_path FROM about WHERE id = 1");
$about = $stmt->fetch();
$file_path = $about['cv_path'] ?? '';

// Check if file exists
if (!empty($file_path) && file_exists($file_path)) {
    // Get file extension
    $ext = pathinfo($file_path, PATHINFO_EXTENSION);

    // Set content type
    $content_type = 'application/octet-stream';
    if ($ext === 'pdf') {
        $content_type = 'application/pdf';
    } elseif ($ext === 'doc') {
        $content_type = 'application/msword';
    } elseif ($ext === 'docx') {
        $content_type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    }

    // Define filename for download
    $filename = 'Resume_Amin_Ullah.' . $ext;

    // Set headers
    header('Content-Description: File Transfer');
    header('Content-Type: ' . $content_type);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));

    // Clear output buffer
    if (ob_get_level()) {
        ob_end_clean();
    }

    // Read file
    readfile($file_path);
    exit;
} else {
    // Handle error
    die("Error: CV file not found. Please contact the administrator.");
}
?>