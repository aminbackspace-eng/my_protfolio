<?php
require_once 'includes/header.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_edu'])) {
    $id = $_POST['id'];
    $degree = trim($_POST['degree']);
    $institution = trim($_POST['institution']);
    $duration = trim($_POST['duration']);
    $desc = trim($_POST['description']);

    if ($id) {
        $pdo->prepare("UPDATE education SET degree=?, institution=?, duration=?, description=? WHERE id=?")
            ->execute([$degree, $institution, $duration, $desc, $id]);
        $success = "Academic credential synchronized.";
    } else {
        $pdo->prepare("INSERT INTO education (degree, institution, duration, description) VALUES (?, ?, ?, ?)")
            ->execute([$degree, $institution, $duration, $desc]);
        $success = "New academic qualification integrated.";
    }
    header("Location: education.php?success=" . urlencode($success));
    exit;
}

if ($action == 'delete' && isset($_GET['id'])) {
    $pdo->prepare("DELETE FROM education WHERE id = ?")->execute([$_GET['id']]);
    header("Location: education.php?success=" . urlencode("Education record removed."));
    exit;
}

if (isset($_GET['success']))
    $msg = $_GET['success'];

$educations = $pdo->query("SELECT * FROM education ORDER BY sort_order ASC, id DESC")->fetchAll();
$edit_edu = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM education WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_edu = $stmt->fetch();
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1 style="font-size: 1.8rem;">Academic Credentials</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Manage your degrees, certifications, and lifelong
            learning</p>
    </div>
    <?php if ($action == 'list'): ?>
        <a href="education.php?action=add" class="btn btn-primary">
            <i class="fas fa-graduation-cap"></i> New Qualification
        </a>
    <?php else: ?>
        <a href="education.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Fleet
        </a>
    <?php endif; ?>
</div>

<?php if ($msg): ?>
    <div
        style="background: rgba(0, 229, 255, 0.1); border: 1px solid var(--primary); color: var(--primary); padding: 15px 20px; border-radius: 12px; margin-bottom: 35px; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-certificate"></i> <?php echo $msg; ?>
    </div>
<?php endif; ?>

<?php if ($action == 'list'): ?>
    <div class="card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Credential & Institution</th>
                        <th>Academic Period</th>
                        <th>Summary</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($educations as $edu): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div
                                        style="width: 45px; height: 45px; background: rgba(0, 229, 255, 0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary); border: 1px solid var(--border);">
                                        <i class="fas fa-university"></i>
                                    </div>
                                    <div style="display: flex; flex-direction: column;">
                                        <span
                                            style="font-weight: 600; color: #fff;"><?php echo htmlspecialchars($edu['degree']); ?></span>
                                        <span
                                            style="font-size: 0.85rem; color: var(--text-muted);"><?php echo htmlspecialchars($edu['institution']); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-read"
                                    style="letter-spacing: 0.5px;"><?php echo htmlspecialchars($edu['duration']); ?></span>
                            </td>
                            <td>
                                <p
                                    style="font-size: 0.85rem; color: var(--text-muted); max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?php echo htmlspecialchars($edu['description']); ?>
                                </p>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                    <a href="education.php?action=edit&id=<?php echo $edu['id']; ?>" class="btn btn-secondary"
                                        style="padding: 8px 12px; font-size: 0.8rem;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="education.php?action=delete&id=<?php echo $edu['id']; ?>" class="btn btn-secondary"
                                        style="padding: 8px 12px; font-size: 0.8rem; color: #ff4d4d;"
                                        onclick="return confirm('Remove this academic record?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($educations)): ?>
                        <tr>
                            <td colspan="4" style="text-align:center; padding: 60px; color: var(--text-muted);">No academic
                                records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($action == 'add' || $action == 'edit'): ?>
    <div class="card" style="max-width: 850px;">
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $edit_edu ? $edit_edu['id'] : ''; ?>">

            <div class="grid-2">
                <div class="form-group">
                    <label>Qualification Title</label>
                    <input type="text" name="degree" class="form-control"
                        value="<?php echo $edit_edu ? htmlspecialchars($edit_edu['degree']) : ''; ?>"
                        placeholder="e.g. Master's in Computer Science" required>
                </div>
                <div class="form-group">
                    <label>Educational Institution</label>
                    <input type="text" name="institution" class="form-control"
                        value="<?php echo $edit_edu ? htmlspecialchars($edit_edu['institution']) : ''; ?>"
                        placeholder="e.g. Stanford University" required>
                </div>
            </div>

            <div class="form-group">
                <label>Academic Period</label>
                <div style="position: relative;">
                    <i class="fas fa-hourglass-half"
                        style="position: absolute; left: 15px; top: 15px; color: var(--primary);"></i>
                    <input type="text" name="duration" class="form-control" style="padding-left: 45px;"
                        value="<?php echo $edit_edu ? htmlspecialchars($edit_edu['duration']) : ''; ?>"
                        placeholder="e.g. 2018 — 2022">
                </div>
            </div>

            <div class="form-group">
                <label>Educational Overview (Optional)</label>
                <textarea name="description" class="form-control" rows="6"
                    placeholder="Honors, Thesis, or key learning modules..."><?php echo $edit_edu ? htmlspecialchars($edit_edu['description']) : ''; ?></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                <button type="submit" name="save_edu" class="btn btn-primary" style="padding: 14px 40px;">
                    <i class="fas fa-save"></i> <?php echo $edit_edu ? 'Update Credential' : 'Commit Credential'; ?>
                </button>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>