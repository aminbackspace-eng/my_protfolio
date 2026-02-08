<?php
require_once 'includes/header.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_exp'])) {
    $id = $_POST['id'];
    $title = trim($_POST['job_title']);
    $company = trim($_POST['company']);
    $duration = trim($_POST['duration']);
    $desc = trim($_POST['description']);

    if ($id) {
        $pdo->prepare("UPDATE experience SET job_title=?, company=?, duration=?, description=? WHERE id=?")
            ->execute([$title, $company, $duration, $desc, $id]);
        $success = "Career milestone updated successfully.";
    } else {
        $pdo->prepare("INSERT INTO experience (job_title, company, duration, description) VALUES (?, ?, ?, ?)")
            ->execute([$title, $company, $duration, $desc]);
        $success = "New professional experience integrated.";
    }
    header("Location: experience.php?success=" . urlencode($success));
    exit;
}

if ($action == 'delete' && isset($_GET['id'])) {
    $pdo->prepare("DELETE FROM experience WHERE id = ?")->execute([$_GET['id']]);
    header("Location: experience.php?success=" . urlencode("Experience record archived."));
    exit;
}

if (isset($_GET['success']))
    $msg = $_GET['success'];

$experiences = $pdo->query("SELECT * FROM experience ORDER BY sort_order ASC, id DESC")->fetchAll();
$edit_exp = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM experience WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_exp = $stmt->fetch();
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1 style="font-size: 1.8rem;">Professional Journey</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Chronicle your career path and key achievements</p>
    </div>
    <?php if ($action == 'list'): ?>
        <a href="experience.php?action=add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Experience
        </a>
    <?php else: ?>
        <a href="experience.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Journey
        </a>
    <?php endif; ?>
</div>

<?php if ($msg): ?>
    <div
        style="background: rgba(0, 229, 255, 0.1); border: 1px solid var(--primary); color: var(--primary); padding: 15px 20px; border-radius: 12px; margin-bottom: 35px; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-briefcase"></i> <?php echo $msg; ?>
    </div>
<?php endif; ?>

<?php if ($action == 'list'): ?>
    <div class="card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Role & Company</th>
                        <th>Duration</th>
                        <th>Brief Summary</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($experiences as $e): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div
                                        style="width: 45px; height: 45px; background: rgba(108, 92, 231, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--secondary); border: 1px solid rgba(108, 92, 231, 0.2);">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div style="display: flex; flex-direction: column;">
                                        <span
                                            style="font-weight: 600; color: #fff;"><?php echo htmlspecialchars($e['job_title']); ?></span>
                                        <span
                                            style="font-size: 0.85rem; color: var(--primary);"><?php echo htmlspecialchars($e['company']); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-read"
                                    style="background: rgba(255,255,255,0.05); color: var(--text-muted);"><?php echo htmlspecialchars($e['duration']); ?></span>
                            </td>
                            <td>
                                <p
                                    style="font-size: 0.85rem; color: var(--text-muted); max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?php echo htmlspecialchars($e['description']); ?>
                                </p>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                    <a href="experience.php?action=edit&id=<?php echo $e['id']; ?>" class="btn btn-secondary"
                                        style="padding: 8px 12px; font-size: 0.8rem;">
                                        <i class="fas fa-pen-nib"></i>
                                    </a>
                                    <a href="experience.php?action=delete&id=<?php echo $e['id']; ?>" class="btn btn-secondary"
                                        style="padding: 8px 12px; font-size: 0.8rem; color: #ff4d4d;"
                                        onclick="return confirm('Archive this experience?')">
                                        <i class="fas fa-archive"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($experiences)): ?>
                        <tr>
                            <td colspan="4" style="text-align:center; padding: 60px; color: var(--text-muted);">No career
                                history recorded yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($action == 'add' || $action == 'edit'): ?>
    <div class="card" style="max-width: 900px;">
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $edit_exp ? $edit_exp['id'] : ''; ?>">

            <div class="grid-2">
                <div class="form-group">
                    <label>Professional Designation</label>
                    <input type="text" name="job_title" class="form-control"
                        value="<?php echo $edit_exp ? htmlspecialchars($edit_exp['job_title']) : ''; ?>"
                        placeholder="e.g. Senior Software Engineer" required>
                </div>
                <div class="form-group">
                    <label>Corporate Identity (Company)</label>
                    <input type="text" name="company" class="form-control"
                        value="<?php echo $edit_exp ? htmlspecialchars($edit_exp['company']) : ''; ?>"
                        placeholder="e.g. Google, Tech Nexus" required>
                </div>
            </div>

            <div class="form-group">
                <label>Tenure / Duration</label>
                <div style="position: relative;">
                    <i class="far fa-calendar-alt"
                        style="position: absolute; left: 15px; top: 15px; color: var(--primary);"></i>
                    <input type="text" name="duration" class="form-control" style="padding-left: 45px;"
                        value="<?php echo $edit_exp ? htmlspecialchars($edit_exp['duration']) : ''; ?>"
                        placeholder="e.g. Jan 2021 — Present">
                </div>
            </div>

            <div class="form-group">
                <label>Key Responsibilities & Achievements</label>
                <textarea name="description" class="form-control" rows="8"
                    placeholder="Outline your core impact and technologies utilized..."><?php echo $edit_exp ? htmlspecialchars($edit_exp['description']) : ''; ?></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 20px;">
                <button type="submit" name="save_exp" class="btn btn-primary" style="padding: 14px 40px;">
                    <i class="fas fa-save"></i> <?php echo $edit_exp ? 'Update Milestone' : 'Commit Milestone'; ?>
                </button>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>