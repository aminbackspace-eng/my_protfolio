<?php
$host = 'localhost';
$dbname = 'my_portfolio';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $pdo->exec("USE $dbname");

    // Tables Creation
    $queries = [
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            full_name VARCHAR(100),
            role ENUM('admin', 'editor') DEFAULT 'admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS about (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255),
            subtitle TEXT,
            description TEXT,
            experience_years VARCHAR(10),
            projects_completed VARCHAR(10),
            happy_clients VARCHAR(10),
            image_path VARCHAR(255),
            cv_path VARCHAR(255),
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            category VARCHAR(100),
            description TEXT,
            image_path VARCHAR(255),
            technologies VARCHAR(255),
            live_link VARCHAR(255),
            github_link VARCHAR(255),
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS skills (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            category VARCHAR(100),
            proficiency INT,
            icon VARCHAR(50),
            sort_order INT DEFAULT 0
        )",
        "CREATE TABLE IF NOT EXISTS experience (
            id INT AUTO_INCREMENT PRIMARY KEY,
            job_title VARCHAR(255) NOT NULL,
            company VARCHAR(255) NOT NULL,
            duration VARCHAR(100),
            description TEXT,
            sort_order INT DEFAULT 0
        )",
        "CREATE TABLE IF NOT EXISTS education (
            id INT AUTO_INCREMENT PRIMARY KEY,
            degree VARCHAR(255) NOT NULL,
            institution VARCHAR(255) NOT NULL,
            duration VARCHAR(100),
            description TEXT,
            sort_order INT DEFAULT 0
        )",
        "CREATE TABLE IF NOT EXISTS services (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            icon VARCHAR(50),
            sort_order INT DEFAULT 0
        )",
        "CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            subject VARCHAR(255),
            message TEXT NOT NULL,
            is_read TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            site_name VARCHAR(100) DEFAULT 'Dev Portfolio',
            site_title VARCHAR(255),
            meta_description TEXT,
            contact_email VARCHAR(100),
            contact_phone VARCHAR(20),
            whatsapp_number VARCHAR(20),
            linkedin_url VARCHAR(255),
            location VARCHAR(255),
            footer_text TEXT,
            logo_path VARCHAR(255)
        )",
        "CREATE TABLE IF NOT EXISTS social_links (
            id INT AUTO_INCREMENT PRIMARY KEY,
            platform VARCHAR(50) NOT NULL,
            url VARCHAR(255) NOT NULL,
            icon VARCHAR(50)
        )"
    ];

    foreach ($queries as $query) {
        $pdo->exec($query);
    }

    // Add new columns to settings if they don't exist
    try {
        $pdo->exec("ALTER TABLE settings ADD COLUMN whatsapp_number VARCHAR(20) AFTER contact_phone");
    } catch (PDOException $e) {
        // Column already exists, ignore
    }
    try {
        $pdo->exec("ALTER TABLE settings ADD COLUMN linkedin_url VARCHAR(255) AFTER whatsapp_number");
    } catch (PDOException $e) {
        // Column already exists, ignore
    }

    // Default Admin User (admin / admin123)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (username, password, email, full_name) VALUES (?, ?, ?, ?)")
            ->execute(['admin', $hashed_password, 'admin@example.com', 'System Admin']);
    }

    // Default About Info
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM about");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $pdo->prepare("INSERT INTO about (title, subtitle, description, experience_years, projects_completed, happy_clients) VALUES (?, ?, ?, ?, ?, ?)")
            ->execute(['Turning Vision Into Reality', 'Web Developer', 'Passionate web developer specializing in creating responsive and user-friendly websites using HTML, CSS, JavaScript, PHP and MySQL.', '2+', '15+', '8+']);
    }

    // Default Settings with WhatsApp and LinkedIn
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM settings");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $pdo->prepare("INSERT INTO settings (site_name, contact_email, contact_phone, whatsapp_number, linkedin_url, location) VALUES (?, ?, ?, ?, ?, ?)")
            ->execute(['DEV.PORTFOLIO', 'hello@example.com', '+923XXXXXXXXX', '923XXXXXXXXX', 'https://linkedin.com/in/yourprofile', 'Pakistan']);
    } else {
        // Update existing settings to add whatsapp and linkedin if empty
        $pdo->exec("UPDATE settings SET whatsapp_number = '923XXXXXXXXX', linkedin_url = 'https://linkedin.com/in/yourprofile' WHERE id = 1 AND (whatsapp_number IS NULL OR whatsapp_number = '')");
    }

    // Clear existing skills and add only real skills
    $pdo->exec("DELETE FROM skills");

    // Insert only the real skills with honest proficiency levels
    $real_skills = [
        // Frontend Skills
        ['HTML', 'Frontend', 94, 'fab fa-html5', 1],
        ['CSS', 'Frontend', 94, 'fab fa-css3-alt', 2],
        ['JavaScript', 'Frontend', 60, 'fab fa-js-square', 3],
        // Backend Skills
        ['PHP', 'Backend', 40, 'fab fa-php', 1],
        ['MySQL', 'Backend', 60, 'fas fa-database', 2],
    ];

    $stmt = $pdo->prepare("INSERT INTO skills (name, category, proficiency, icon, sort_order) VALUES (?, ?, ?, ?, ?)");
    foreach ($real_skills as $skill) {
        $stmt->execute($skill);
    }

    echo "Database setup completed successfully!<br>";
    echo "✓ Tables created/verified<br>";
    echo "✓ Skills updated with honest levels:<br>";
    echo "&nbsp;&nbsp;- HTML: 94%<br>";
    echo "&nbsp;&nbsp;- CSS: 94%<br>";
    echo "&nbsp;&nbsp;- JavaScript: 60%<br>";
    echo "&nbsp;&nbsp;- PHP: 40%<br>";
    echo "&nbsp;&nbsp;- MySQL: 60%<br>";
    echo "✓ Settings updated with WhatsApp and LinkedIn fields<br>";
    echo "<br><strong>IMPORTANT:</strong> Update your settings in the admin panel with your actual:<br>";
    echo "- WhatsApp number (format: 923XXXXXXXXX without + sign)<br>";
    echo "- LinkedIn profile URL<br>";
    echo "- Contact email<br>";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>