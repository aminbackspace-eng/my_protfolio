<?php
require_once 'includes/header.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_service'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $icon = $_POST['icon'];

    if ($id) {
        $pdo->prepare("UPDATE services SET title=?, description=?, icon=? WHERE id=?")
            ->execute([$title, $desc, $icon, $id]);
        $msg = "Service offering updated.";
    } else {
        $pdo->prepare("INSERT INTO services (title, description, icon) VALUES (?, ?, ?)")
            ->execute([$title, $desc, $icon]);
        $msg = "New service offering listed.";
    }
    $action = 'list';
}

if ($action == 'delete' && isset($_GET['id'])) {
    $pdo->prepare("DELETE FROM services WHERE id = ?")->execute([$_GET['id']]);
    $msg = "Service offering removed.";
    $action = 'list';
}

$services = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC, id DESC")->fetchAll();
$edit_service = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_service = $stmt->fetch();
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1 style="font-size: 1.8rem;">Solution Offerings</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Define the professional services and solutions you
            provide</p>
    </div>
    <?php if ($action == 'list'): ?>
        <a href="services.php?action=add" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> New Offering
        </a>
    <?php else: ?>
        <a href="services.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Fleet
        </a>
    <?php endif; ?>
</div>

<?php if ($msg): ?>
    <div
        style="background: rgba(0, 229, 255, 0.1); border: 1px solid var(--primary); color: var(--primary); padding: 15px 20px; border-radius: 12px; margin-bottom: 35px; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-layer-group"></i> <?php echo $msg; ?>
    </div>
<?php endif; ?>

<?php if ($action == 'list'): ?>
    <div class="card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px;">Visual</th>
                        <th>Service Strategy</th>
                        <th>Scope Overview</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $s): ?>
                        <tr>
                            <td>
                                <div
                                    style="width: 50px; height: 50px; background: rgba(0, 229, 255, 0.05); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary); border: 1px solid var(--border);">
                                    <i class="<?php echo $s['icon']; ?>" style="font-size: 1.4rem;"></i>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #fff;"><?php echo htmlspecialchars($s['title']); ?></span>
                            </td>
                            <td>
                                <p style="font-size: 0.85rem; color: var(--text-muted); max-width: 400px; line-height: 1.4;">
                                    <?php echo htmlspecialchars($s['description']); ?>
                                </p>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                    <a href="services.php?action=edit&id=<?php echo $s['id']; ?>" class="btn btn-secondary"
                                        style="padding: 8px 12px; font-size: 0.8rem;">
                                        <i class="fas fa-sliders-h"></i>
                                    </a>
                                    <a href="services.php?action=delete&id=<?php echo $s['id']; ?>" class="btn btn-secondary"
                                        style="padding: 8px 12px; font-size: 0.8rem; color: #ff4d4d;"
                                        onclick="return confirm('Retire this service offering?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($services)): ?>
                        <tr>
                            <td colspan="4" style="text-align:center; padding: 60px; color: var(--text-muted);">No service
                                offerings listed yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($action == 'add' || $action == 'edit'): ?>
    <div class="card" style="max-width: 800px;">
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $edit_service ? $edit_service['id'] : ''; ?>">

            <div class="form-group">
                <label>Service Designation (Title)</label>
                <input type="text" name="title" class="form-control"
                    value="<?php echo $edit_service ? htmlspecialchars($edit_service['title']) : ''; ?>"
                    placeholder="e.g. Full-Stack Web Engineering" required>
            </div>

            <div class="form-group">
                <label>Visual Identifier (Icon Class)</label>
                <div style="display: flex; gap: 15px; align-items: center;">
                    <div
                        style="width: 55px; height: 55px; background: var(--darker); border-radius: 12px; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <i id="icon-preview"
                            class="<?php echo $edit_service ? htmlspecialchars($edit_service['icon']) : 'fas fa-rocket'; ?>"
                            style="font-size: 1.5rem;"></i>
                    </div>
                    <input type="text" name="icon" class="form-control"
                        value="<?php echo $edit_service ? htmlspecialchars($edit_service['icon']) : 'fas fa-rocket'; ?>"
                        placeholder="fas fa-laptop-code"
                        oninput="document.getElementById('icon-preview').className = this.value">
                </div>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 10px;">Preview updates instantly as you
                    type FontAwesome 6 classes.</p>
            </div>

            <div class="form-group">
                <label>Service Narrative (Scope & Delivery)</label>
                <textarea name="description" class="form-control" rows="6"
                    placeholder="Describe the value proposition and key deliverables..."><?php echo $edit_service ? htmlspecialchars($edit_service['description']) : ''; ?></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 25px;">
                <button type="submit" name="save_service" class="btn btn-primary" style="padding: 14px 45px;">
                    <i class="fas fa-save"></i> <?php echo $edit_service ? 'Update Strategy' : 'List Strategy'; ?>
                </button>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>