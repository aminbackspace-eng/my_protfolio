<?php
require_once 'includes/header.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_settings'])) {
    try {
        $site_name = trim($_POST['site_name'] ?? '');
        $site_title = trim($_POST['site_title'] ?? '');
        $meta_description = trim($_POST['meta_description'] ?? '');
        $contact_email = trim($_POST['contact_email'] ?? '');
        $contact_phone = trim($_POST['contact_phone'] ?? '');
        $whatsapp_number = trim($_POST['whatsapp_number'] ?? '');
        $linkedin_url = trim($_POST['linkedin_url'] ?? '');
        $location = trim($_POST['location'] ?? '');

        $stmt = $pdo->prepare("UPDATE settings SET site_name=?, site_title=?, meta_description=?, contact_email=?, contact_phone=?, whatsapp_number=?, linkedin_url=?, location=? WHERE id=1");
        $stmt->execute([$site_name, $site_title, $meta_description, $contact_email, $contact_phone, $whatsapp_number, $linkedin_url, $location]);

        // Synchronize with about table for the metrics
        $stmt = $pdo->prepare("UPDATE about SET title=?, description=?, experience_years=?, projects_completed=?, happy_clients=? WHERE id=1");
        $stmt->execute([
            trim($_POST['about_title'] ?? ''),
            trim($_POST['about_desc'] ?? ''),
            trim($_POST['exp_years'] ?? ''),
            trim($_POST['proj_count'] ?? ''),
            trim($_POST['client_count'] ?? '')
        ]);

        header("Location: settings.php?success=" . urlencode("Global configuration synchronized successfully!"));
        exit;
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
        header("Location: settings.php?error=" . urlencode($error));
        exit;
    }
}

if (isset($_GET['success'])) {
    $msg = $_GET['success'];
    $msgType = "success";
} elseif (isset($_GET['error'])) {
    $msg = $_GET['error'];
    $msgType = "error";
}

$settings = $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch() ?: [];
$about = $pdo->query("SELECT * FROM about WHERE id = 1")->fetch() ?: [];
?>

<div style="margin-bottom: 35px;">
    <h1 style="font-size: 1.8rem;">System Configuration</h1>
    <p style="color: var(--text-muted); font-size: 0.9rem;">Global parameters and SEO meta-data management</p>
</div>

<?php if ($msg): ?>
    <div
        style="background: <?php echo ($msgType ?? 'success') == 'error' ? 'rgba(255, 71, 87, 0.1)' : 'rgba(0, 229, 255, 0.1)'; ?>; 
               border: 1px solid <?php echo ($msgType ?? 'success') == 'error' ? '#ff4757' : 'var(--primary)'; ?>; 
               color: <?php echo ($msgType ?? 'success') == 'error' ? '#ff4757' : 'var(--primary)'; ?>; 
               padding: 15px 20px; border-radius: 12px; margin-bottom: 35px; display: flex; align-items: center; gap: 10px;">
        <i class="fas <?php echo ($msgType ?? 'success') == 'error' ? 'fa-exclamation-circle' : 'fa-sync-alt'; ?>"></i>
        <?php echo $msg; ?>
    </div>
<?php endif; ?>

<form method="POST">
    <div class="grid-2">
        <div>
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-globe" style="margin-right: 10px; color: var(--primary);"></i> SEO & Site
                        Identity</h3>
                </div>
                <div class="form-group">
                    <label>Brand Identity (Site Name)</label>
                    <input type="text" name="site_name" class="form-control"
                        value="<?php echo htmlspecialchars($settings['site_name']); ?>" placeholder="e.g. Portfolio v2">
                </div>
                <div class="form-group">
                    <label>Browser Page Title (SEO)</label>
                    <input type="text" name="site_title" class="form-control"
                        value="<?php echo htmlspecialchars($settings['site_title']); ?>"
                        placeholder="Web Developer | Full Stack Specialist">
                </div>
                <div class="form-group">
                    <label>Meta Description (Search Engines)</label>
                    <textarea name="meta_description" class="form-control" rows="4"
                        placeholder="Briefly describe your portfolio for Google results..."><?php echo htmlspecialchars($settings['meta_description']); ?></textarea>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-paper-plane" style="margin-right: 10px; color: var(--primary);"></i>
                        Communication Access</h3>
                </div>
                <div class="form-group">
                    <label>Primary Email Gateway</label>
                    <input type="email" name="contact_email" class="form-control"
                        value="<?php echo htmlspecialchars($settings['contact_email']); ?>">
                </div>
                <div class="form-group">
                    <label>Contact Number (Display)</label>
                    <input type="text" name="contact_phone" class="form-control"
                        value="<?php echo htmlspecialchars($settings['contact_phone']); ?>">
                </div>
                <div class="form-group">
                    <label><i class="fab fa-whatsapp" style="color: #25D366;"></i> WhatsApp Number</label>
                    <input type="text" name="whatsapp_number" class="form-control"
                        value="<?php echo htmlspecialchars($settings['whatsapp_number'] ?? ''); ?>"
                        placeholder="923XXXXXXXXX (without + sign)">
                    <small style="color: var(--text-muted); font-size: 0.75rem;">Format: Country code + number without +
                        sign (e.g., 923001234567)</small>
                </div>
                <div class="form-group">
                    <label><i class="fab fa-linkedin" style="color: #0077B5;"></i> LinkedIn Profile URL</label>
                    <input type="url" name="linkedin_url" class="form-control"
                        value="<?php echo htmlspecialchars($settings['linkedin_url'] ?? ''); ?>"
                        placeholder="https://linkedin.com/in/yourprofile">
                </div>
                <div class="form-group">
                    <label>Work Base (Location)</label>
                    <input type="text" name="location" class="form-control"
                        value="<?php echo htmlspecialchars($settings['location']); ?>">
                </div>
            </div>
        </div>

        <div>
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-user-circle" style="margin-right: 10px; color: var(--primary);"></i> Hero &
                        About Data</h3>
                </div>
                <div class="form-group">
                    <label>About Heading Prefix</label>
                    <input type="text" name="about_title" class="form-control"
                        value="<?php echo htmlspecialchars($about['title']); ?>">
                </div>
                <div class="form-group">
                    <label>Professional Bio</label>
                    <textarea name="about_desc" class="form-control"
                        rows="7"><?php echo htmlspecialchars($about['description']); ?></textarea>
                </div>

                <h4
                    style="margin: 25px 0 15px; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">
                    Engagement Metrics</h4>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                    <div class="form-group">
                        <label>Exp (Years)</label>
                        <input type="text" name="exp_years" class="form-control"
                            value="<?php echo htmlspecialchars($about['experience_years']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Projects</label>
                        <input type="text" name="proj_count" class="form-control"
                            value="<?php echo htmlspecialchars($about['projects_completed']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Clients</label>
                        <input type="text" name="client_count" class="form-control"
                            value="<?php echo htmlspecialchars($about['happy_clients']); ?>">
                    </div>
                </div>
            </div>

            <div
                style="background: rgba(108, 92, 231, 0.05); border: 1px dashed var(--secondary); padding: 25px; border-radius: 20px; text-align: center;">
                <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 20px;">Backup your database
                    regularly to prevent data loss during configuration changes.</p>
                <button type="submit" name="save_settings" class="btn btn-primary" style="width: 100%; padding: 16px;">
                    <i class="fas fa-save"></i> Synchronize All Parameters
                </button>
            </div>
        </div>
    </div>
</form>

<?php require_once 'includes/footer.php'; ?>