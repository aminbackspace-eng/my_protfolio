<?php
session_start();
require_once __DIR__ . '/../../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Dev Portfolio</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>

<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="admin-logo">
                <h2>DEV.<span>CMS</span></h2>
                <p>Management Hub</p>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php" class="<?php echo $page == 'index.php' ? 'active' : ''; ?>"><i
                                class="fas fa-grid-2"></i> <span>Dashboard</span></a></li>
                    <li><a href="about.php" class="<?php echo $page == 'about.php' ? 'active' : ''; ?>"><i
                                class="fas fa-user-circle"></i> <span>About Me</span></a></li>
                    <li><a href="projects.php" class="<?php echo $page == 'projects.php' ? 'active' : ''; ?>"><i
                                class="fas fa-rocket"></i> <span>Projects</span></a></li>
                    <li><a href="skills.php" class="<?php echo $page == 'skills.php' ? 'active' : ''; ?>"><i
                                class="fas fa-bolt"></i> <span>Skills Matrix</span></a></li>
                    <li><a href="experience.php" class="<?php echo $page == 'experience.php' ? 'active' : ''; ?>"><i
                                class="fas fa-briefcase"></i> <span>Experience</span></a></li>
                    <li><a href="education.php" class="<?php echo $page == 'education.php' ? 'active' : ''; ?>"><i
                                class="fas fa-graduation-cap"></i> <span>Education</span></a></li>
                    <li><a href="services.php" class="<?php echo $page == 'services.php' ? 'active' : ''; ?>"><i
                                class="fas fa-shapes"></i> <span>Services</span></a></li>
                    <li><a href="messages.php" class="<?php echo $page == 'messages.php' ? 'active' : ''; ?>"><i
                                class="fas fa-comment-dots"></i> <span>Messages</span></a></li>
                    <li><a href="settings.php" class="<?php echo $page == 'settings.php' ? 'active' : ''; ?>"><i
                                class="fas fa-sliders"></i> <span>Settings</span></a></li>
                    <li><a href="logout.php"><i class="fas fa-power-off"></i> <span>Logout</span></a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <div>
                    <h2 style="font-size: 1.5rem;"><?php
                    switch ($page) {
                        case 'index.php':
                            echo 'Dashboard Overview';
                            break;
                        case 'about.php':
                            echo 'About Me Section';
                            break;
                        case 'projects.php':
                            echo 'Project Portfolio';
                            break;
                        case 'skills.php':
                            echo 'Technical Skills';
                            break;
                        case 'experience.php':
                            echo 'Work History';
                            break;
                        case 'education.php':
                            echo 'Academic Background';
                            break;
                        case 'services.php':
                            echo 'My Services';
                            break;
                        case 'messages.php':
                            echo 'Inquiries';
                            break;
                        case 'settings.php':
                            echo 'System Settings';
                            break;
                        default:
                            echo 'Management';
                    }
                    ?></h2>
                </div>
                <div class="user-info">
                    <?php $adminName = $_SESSION['admin_name'] ?? 'Admin'; ?>
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($adminName, 0, 1)); ?>
                    </div>
                    <span><?php echo htmlspecialchars($adminName); ?></span>
                </div>
            </header>