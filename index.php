<?php
require_once 'includes/db.php';

// Fetch Site Settings
$settings = $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch();

// Fetch About Info
$about = $pdo->query("SELECT * FROM about WHERE id = 1")->fetch();

// Fetch Projects
$projects = $pdo->query("SELECT * FROM projects ORDER BY sort_order ASC, created_at DESC")->fetchAll();

// Fetch Skills by Category
$skills_query = $pdo->query("SELECT * FROM skills ORDER BY sort_order ASC");
$skills_by_cat = [];
while ($row = $skills_query->fetch()) {
    $skills_by_cat[$row['category']][] = $row;
}

// Fetch Experience
$experiences = $pdo->query("SELECT * FROM experience ORDER BY sort_order ASC")->fetchAll();

// Fetch Education
$educations = $pdo->query("SELECT * FROM education ORDER BY sort_order ASC")->fetchAll();

// Fetch Services
$services = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC")->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($settings['site_title'] ?? 'Web Developer Portfolio'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($settings['meta_description'] ?? ''); ?>">
    <meta name="author" content="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url"
        content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($settings['site_title'] ?? ''); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($settings['meta_description'] ?? ''); ?>">
    <meta property="og:image"
        content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]"; ?>/assets/img/profile.jpg">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($settings['site_title'] ?? ''); ?>">
    <meta property="twitter:description" content="<?php echo htmlspecialchars($settings['meta_description'] ?? ''); ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/img/favicon.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">

    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>

<body class="dark-theme">
    <!-- Loader -->
    <div class="loader-wrapper">
        <div class="loader"></div>
    </div>

    <!-- Header / Navigation -->
    <header class="header">
        <nav class="container nav">
            <a href="#" class="logo">
                <span class="logo-dot"></span> DEV.<span>PORTFOLIO</span>
            </a>

            <ul class="nav-links">
                <li><a href="#home" class="nav-link active">Home</a></li>
                <li><a href="#about" class="nav-link">About</a></li>
                <li><a href="#services" class="nav-link">Services</a></li>
                <li><a href="#skills" class="nav-link">Skills</a></li>
                <li><a href="#projects" class="nav-link">Projects</a></li>
                <li><a href="#contact" class="nav-link">Contact</a></li>
            </ul>

            <div class="nav-actions">
                <button id="theme-toggle" class="theme-toggle" aria-label="Toggle Theme">
                    <i class="fas fa-moon"></i>
                </button>
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <!-- Hero Section -->
        <section id="home" class="hero section">
            <div class="container hero-grid">
                <div class="hero-content" data-aos="fade-right" data-aos-duration="1000">
                    <span class="hero-badge">Available for new opportunities</span>
                    <h1 class="hero-title">
                        I'm <span
                            class="gradient-text"><?php echo htmlspecialchars($settings['site_name'] ?? 'Amin Ullah'); ?></span><br>
                        Building Digital Experiences
                    </h1>
                    <p class="hero-subtitle">
                        <?php echo $about['subtitle'] ?? "Hi, I'm a passionate Full-Stack Web Developer specializing in creating high-performance, user-centric web applications and modern digital solutions."; ?>
                    </p>
                    <div class="hero-btns">
                        <a href="#projects" class="btn btn-primary">View My Work</a>
                        <a href="#contact" class="btn btn-secondary">Get In Touch</a>
                        <?php if (!empty($about['cv_path'])): ?>
                            <a href="download_cv.php" class="btn btn-outline" download style="border-radius: 100px;">
                                <i class="fas fa-file-pdf"></i> Download CV
                            </a>
                        <?php else: ?>
                            <button onclick="window.print()" class="btn btn-outline" style="border-radius: 100px;">
                                <i class="fas fa-print"></i> Print Resume
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hero-image" data-aos="fade-left" data-aos-duration="1000">
                    <div class="image-orb"></div>
                    <img src="assets/img/profile.jpg" alt="Amin Ullah - Web Developer"
                        class="floating hero-profile-img">
                </div>
            </div>
            <a href="#about" class="scroll-down">
                <span>Scroll Down</span>
                <i class="fas fa-chevron-down"></i>
            </a>
        </section>

        <!-- About Section -->
        <section id="about" class="about section">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <span class="section-subtitle">Introduction</span>
                    <h2 class="section-title">About Me</h2>
                </div>
                <div class="about-grid">
                    <div class="about-info" data-aos="fade-right">
                        <h3><?php echo htmlspecialchars($about['title'] ?? 'Turning Vision Into Reality'); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($about['description'] ?? '')); ?></p>
                        <div class="about-stats">
                            <div class="stat-item">
                                <span
                                    class="stat-number"><?php echo htmlspecialchars($about['experience_years'] ?? '0+'); ?></span>
                                <span class="stat-label">Years Experience</span>
                            </div>
                            <div class="stat-item">
                                <span
                                    class="stat-number"><?php echo htmlspecialchars($about['projects_completed'] ?? '0+'); ?></span>
                                <span class="stat-label">Projects Completed</span>
                            </div>
                            <div class="stat-item">
                                <span
                                    class="stat-number"><?php echo htmlspecialchars($about['happy_clients'] ?? '0+'); ?></span>
                                <span class="stat-label">Happy Clients</span>
                            </div>
                        </div>
                    </div>
                    <div class="about-image" data-aos="fade-left">
                        <div class="experience-card">
                            <i class="fas fa-code-branch"></i>
                            <div>
                                <h4>Full Stack Focus</h4>
                                <p>End-to-end development approach</p>
                            </div>
                        </div>
                        <img src="<?php echo htmlspecialchars($about['image_path'] ?? 'assets/img/about-img.jpg'); ?>?v=<?php echo time(); ?>"
                            alt="About Me">
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="services section bg-alt">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <span class="section-subtitle">What I Offer</span>
                    <h2 class="section-title">Professional Services</h2>
                </div>
                <div class="services-grid">
                    <?php if (empty($services)): ?>
                        <p style="text-align:center; grid-column: 1/-1; color: var(--text-muted);">Services are being
                            updated. Check back soon!</p>
                    <?php endif; ?>
                    <?php foreach ($services as $idx => $service): ?>
                        <div class="service-card" data-aos="fade-up" data-aos-delay="<?php echo $idx * 100; ?>">
                            <div class="service-icon">
                                <i class="<?php echo htmlspecialchars($service['icon']); ?>"></i>
                            </div>
                            <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                            <div class="service-curve"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Skills Section -->
        <section id="skills" class="skills section bg-alt">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <span class="section-subtitle">Competencies</span>
                    <h2 class="section-title">My Tech Stack</h2>
                </div>
                <div class="skills-grid">
                    <?php
                    $delay = 100;
                    foreach ($skills_by_cat as $category => $skills): ?>
                        <div class="skill-category" data-aos="zoom-in" data-aos-delay="<?php echo $delay; ?>">
                            <div class="skill-category-header">
                                <i class="<?php
                                if ($category == 'Frontend')
                                    echo 'fas fa-laptop-code';
                                elseif ($category == 'Backend')
                                    echo 'fas fa-server';
                                else
                                    echo 'fas fa-tools';
                                ?>"></i>
                                <h3><?php echo $category; ?> Development</h3>
                            </div>
                            <div class="skill-list">
                                <?php foreach ($skills as $skill): ?>
                                    <div class="skill-item">
                                        <div class="skill-info">
                                            <span><?php echo htmlspecialchars($skill['name']); ?></span>
                                            <span><?php echo $skill['proficiency']; ?>%</span>
                                        </div>
                                        <div class="skill-bar">
                                            <div class="skill-progress" style="width: <?php echo $skill['proficiency']; ?>%;">
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php
                        $delay += 100;
                    endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Projects Section -->
        <section id="projects" class="projects section">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <span class="section-subtitle">Portfolio</span>
                    <h2 class="section-title">Featured Projects</h2>
                </div>

                <div class="projects-filter" data-aos="fade-up">
                    <button class="filter-btn active" data-filter="all">All</button>
                    <?php
                    $cats = array_unique(array_column($projects, 'category'));
                    foreach ($cats as $cat):
                        if (!$cat)
                            continue; ?>
                        <button class="filter-btn"
                            data-filter="<?php echo strtolower(str_replace(' ', '-', $cat)); ?>"><?php echo $cat; ?></button>
                    <?php endforeach; ?>
                </div>

                <div class="projects-grid">
                    <?php foreach ($projects as $idx => $p): ?>
                        <div class="project-card"
                            data-category="<?php echo strtolower(str_replace(' ', '-', $p['category'])); ?>"
                            data-aos="fade-up" data-aos-delay="<?php echo $idx * 100; ?>">
                            <div class="project-image">
                                <img src="<?php echo htmlspecialchars($p['image_path']); ?>"
                                    alt="<?php echo htmlspecialchars($p['title']); ?>" loading="lazy">
                                <div class="project-overlay">
                                    <div class="project-links">
                                        <?php if ($p['live_link']): ?>
                                            <a href="<?php echo $p['live_link']; ?>" target="_blank" title="Live Demo"><i
                                                    class="fas fa-external-link-alt"></i></a>
                                        <?php endif; ?>
                                        <?php if ($p['github_link']): ?>
                                            <a href="<?php echo $p['github_link']; ?>" target="_blank" title="GitHub Repo"><i
                                                    class="fab fa-github"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="project-info">
                                <span class="project-cat"><?php echo htmlspecialchars($p['category']); ?></span>
                                <h3><?php echo htmlspecialchars($p['title']); ?></h3>
                                <p><?php echo htmlspecialchars($p['description']); ?></p>
                                <div class="project-tech">
                                    <?php
                                    $techs = explode(',', $p['technologies']);
                                    foreach ($techs as $t): ?>
                                        <span><?php echo trim($t); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="view-more" data-aos="fade-up">
                    <a href="#" class="btn btn-outline">See All Projects <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </section>

        <!-- Timeline Section (Combined Experience & Education) -->
        <section id="experience" class="experience section bg-alt">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <span class="section-subtitle">Career Path</span>
                    <h2 class="section-title">Education & Experience</h2>
                </div>
                <div class="timeline">
                    <?php foreach ($experiences as $e): ?>
                        <div class="timeline-item" data-aos="fade-up">
                            <div class="timeline-dot"></div>
                            <div class="timeline-date"><?php echo htmlspecialchars($e['duration']); ?></div>
                            <div class="timeline-content">
                                <h3><?php echo htmlspecialchars($e['job_title']); ?></h3>
                                <p class="company"><?php echo htmlspecialchars($e['company']); ?></p>
                                <p><?php echo nl2br(htmlspecialchars($e['description'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php foreach ($educations as $edu): ?>
                        <div class="timeline-item" data-aos="fade-up">
                            <div class="timeline-dot" style="border-color: #ff9f43;"></div>
                            <div class="timeline-date"><?php echo htmlspecialchars($edu['duration']); ?></div>
                            <div class="timeline-content">
                                <h3><?php echo htmlspecialchars($edu['degree']); ?></h3>
                                <p class="company"><?php echo htmlspecialchars($edu['institution']); ?></p>
                                <p><?php echo nl2br(htmlspecialchars($edu['description'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="contact section">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <span class="section-subtitle">Get In Touch</span>
                    <h2 class="section-title">Let's Work Together</h2>
                </div>

                <!-- Professional Contact Buttons -->
                <div class="contact-cta-buttons" data-aos="fade-up" data-aos-delay="100">
                    <?php
                    // Clean WhatsApp number (remove +, -, spaces)
                    $wa_raw = $settings['whatsapp_number'] ?? '';
                    $wa_clean = preg_replace('/[^0-9]/', '', $wa_raw);
                    if (!$wa_clean)
                        $wa_clean = '923XXXXXXXXX'; // Fallback
                    ?>
                    <a href="https://wa.me/<?php echo $wa_clean; ?>" target="_blank" rel="noopener noreferrer"
                        class="contact-cta-btn whatsapp-btn">
                        <div class="btn-icon"><i class="fab fa-whatsapp"></i></div>
                        <span>WhatsApp</span>
                    </a>

                    <a href="#contact-form-section" class="contact-cta-btn contact-btn scroll-to-form">
                        <div class="btn-icon"><i class="fas fa-envelope"></i></div>
                        <span>Email Me</span>
                    </a>

                    <a href="<?php echo htmlspecialchars($settings['linkedin_url'] ?? '#'); ?>" target="_blank"
                        rel="noopener noreferrer" class="contact-cta-btn linkedin-btn">
                        <div class="btn-icon"><i class="fab fa-linkedin-in"></i></div>
                        <span>LinkedIn</span>
                    </a>
                </div>

                <div class="contact-grid">
                    <div class="contact-info" data-aos="fade-right">
                        <a href="mailto:<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>"
                            class="contact-card">
                            <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                            <div>
                                <h4>Email Me</h4>
                                <p><?php echo htmlspecialchars($settings['contact_email'] ?? 'hello@yourdomain.com'); ?>
                                </p>
                            </div>
                        </a>
                        <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $settings['contact_phone'] ?? ''); ?>"
                            class="contact-card">
                            <div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
                            <div>
                                <h4>Call Me</h4>
                                <p><?php echo htmlspecialchars($settings['contact_phone'] ?? '+1 (234) 567-890'); ?></p>
                            </div>
                        </a>
                        <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($settings['location'] ?? ''); ?>"
                            target="_blank" class="contact-card">
                            <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <h4>Location</h4>
                                <p><?php echo htmlspecialchars($settings['location'] ?? 'San Francisco, CA'); ?></p>
                            </div>
                        </a>
                    </div>
                    <div id="contact-form-section" class="contact-form-wrapper" data-aos="fade-left">
                        <form action="includes/contact.php" method="POST" id="contact-form" class="contact-form">
                            <div class="form-group">
                                <input type="text" name="name" id="name" placeholder="Your Name" required>
                            </div>
                            <div class="form-group">
                                <input type="email" name="email" id="email" placeholder="Your Email" required>
                            </div>
                            <div class="form-group">
                                <input type="text" name="subject" id="subject" placeholder="Subject" required>
                            </div>
                            <div class="form-group">
                                <textarea name="message" id="message" rows="5" placeholder="Your Message"
                                    required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-full">Send Message <i
                                    class="fas fa-paper-plane"></i></button>
                            <div id="form-status" class="form-status"></div>
                        </form>
                    </div>
                </div>

                <!-- Location Map -->
                <div class="contact-map" data-aos="fade-up" data-aos-delay="200">
                    <iframe
                        src="https://maps.google.com/maps?q=<?php echo urlencode($settings['location'] ?? 'San Francisco, CA'); ?>&t=&z=13&ie=UTF8&iwloc=&output=embed"
                        title="My Location" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Brand Widget -->
                <div class="footer-widget brand-widget">
                    <a href="#" class="logo">DEV.<span>PORTFOLIO</span></a>
                    <p class="brand-desc">
                        Creating digital experiences that matter. Focused on performance, aesthetics, and user-centric
                        design.
                    </p>
                    <div class="footer-social">
                        <a href="<?php echo htmlspecialchars($settings['linkedin_url'] ?? '#'); ?>" target="_blank"><i
                                class="fab fa-linkedin-in"></i></a>
                        <a href="https://github.com" target="_blank"><i class="fab fa-github"></i></a>
                        <a href="https://twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
                        <a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="footer-widget">
                    <h3 class="widget-title">Quick Links</h3>
                    <ul class="footer-nav">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About Me</a></li>
                        <li><a href="#projects">Portfolio</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div class="footer-widget">
                    <h3 class="widget-title">Services</h3>
                    <ul class="footer-nav">
                        <li><a href="#services">Web Development</a></li>
                        <li><a href="#services">UI/UX Design</a></li>
                        <li><a href="#services">Backend Systems</a></li>
                        <li><a href="#services">API Integration</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="footer-widget">
                    <h3 class="widget-title">Contact</h3>
                    <ul class="footer-nav contact-nav">
                        <li><i class="fas fa-envelope"></i>
                            <?php echo htmlspecialchars($settings['contact_email'] ?? 'hello@example.com'); ?></li>
                        <li><i class="fas fa-phone-alt"></i>
                            <?php echo htmlspecialchars($settings['contact_phone'] ?? '+123 456 7890'); ?></li>
                        <li><i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($settings['location'] ?? 'Remote'); ?></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?>
                    <?php echo htmlspecialchars($settings['site_name'] ?? 'Dev Portfolio'); ?>. All rights reserved.
                </p>
                <div class="footer-links">
                    <a href="admin/">Admin Panel</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    </script>
</body>

</html>