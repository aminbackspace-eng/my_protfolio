<?php
require_once 'db.php';

// Initial Skills
$skills = [
    ['HTML5 / CSS3', 'Frontend', 95, 'fab fa-html5'],
    ['JavaScript (ES6+)', 'Frontend', 90, 'fab fa-js'],
    ['React.js', 'Frontend', 85, 'fab fa-react'],
    ['PHP', 'Backend', 88, 'fab fa-php'],
    ['MySQL', 'Backend', 85, 'fas fa-database'],
    ['Node.js', 'Backend', 75, 'fab fa-node-js'],
    ['Git & GitHub', 'Tools', 92, 'fab fa-github'],
    ['Figma', 'Tools', 80, 'fab fa-figma']
];

foreach ($skills as $s) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM skills WHERE name = ?");
    $stmt->execute([$s[0]]);
    if ($stmt->fetchColumn() == 0) {
        $pdo->prepare("INSERT INTO skills (name, category, proficiency, icon) VALUES (?, ?, ?, ?)")
            ->execute($s);
    }
}

// Initial Projects
$projects = [
    ['LuxeCart Shopping', 'PHP/MySQL', 'Full-featured online store with product management and checkout.', 'images/projects/ecommerce.jpg', 'PHP, MySQL, Bootstrap', '#', '#'],
    ['Streamline Dashboard', 'PHP/MySQL', 'Comprehensive admin interface for managing inventory and analytics.', 'images/projects/admin.jpg', 'PHP, Chart.js, DataTables', '#', '#'],
    ['TaskFlow Pro', 'React', 'Real-time task management application with drag-and-drop.', 'images/projects/task-manager.jpg', 'React, Redux, Firebase', '#', '#'],
    ['Glassmorphism Portfolio', 'Frontend', 'Eye-catching personal portfolio with glassmorphism design.', 'images/projects/portfolio.jpg', 'HTML5, CSS3, AOS JS', '#', '#']
];

foreach ($projects as $p) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE title = ?");
    $stmt->execute([$p[0]]);
    if ($stmt->fetchColumn() == 0) {
        $pdo->prepare("INSERT INTO projects (title, category, description, image_path, technologies, live_link, github_link) VALUES (?, ?, ?, ?, ?, ?, ?)")
            ->execute($p);
    }
}

// Initial Experience
$exp = [
    ['Senior Web Developer', 'Digital Nexus Solutions', '2023 - Present', 'Leading the development of enterprise-level platforms.'],
    ['Full Stack Developer', 'Creative Pulse Media', '2021 - 2023', 'Developed custom PHP/MySQL solutions for various clients.']
];

foreach ($exp as $e) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM experience WHERE job_title = ?");
    $stmt->execute([$e[0]]);
    if ($stmt->fetchColumn() == 0) {
        $pdo->prepare("INSERT INTO experience (job_title, company, duration, description) VALUES (?, ?, ?, ?)")
            ->execute($e);
    }
}

// Initial Education
$edu = [
    ['B.Sc. in Computer Science', 'Tech Global University', '2019 - 2021', 'Specialized in Web Technologies and Database Systems.']
];

foreach ($edu as $ed) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM education WHERE degree = ?");
    $stmt->execute([$ed[0]]);
    if ($stmt->fetchColumn() == 0) {
        $pdo->prepare("INSERT INTO education (degree, institution, duration, description) VALUES (?, ?, ?, ?)")
            ->execute($ed);
    }
}

echo "Initial data seeded successfully.";
?>