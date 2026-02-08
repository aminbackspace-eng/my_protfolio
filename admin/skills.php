<?php
require_once 'includes/header.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_skill'])) {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $proficiency = (int) $_POST['proficiency'];
    $icon = trim($_POST['icon']);

    if ($id) {
        $stmt = $pdo->prepare("UPDATE skills SET name=?, category=?, proficiency=?, icon=? WHERE id=?");
        $stmt->execute([$name, $category, $proficiency, $icon, $id]);
        $success = "Skill metrics synchronized.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO skills (name, category, proficiency, icon) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $category, $proficiency, $icon]);
        $success = "New skill integrated into matrix.";
    }
    header("Location: skills.php?success=" . urlencode($success));
    exit;
}

if ($action == 'delete' && isset($_GET['id'])) {
    $pdo->prepare("DELETE FROM skills WHERE id = ?")->execute([$_GET['id']]);
    header("Location: skills.php?success=" . urlencode("Skill removed from matrix."));
    exit;
}

if (isset($_GET['success']))
    $msg = $_GET['success'];

$skills = $pdo->query("SELECT * FROM skills ORDER BY category, sort_order")->fetchAll();
$edit_skill = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM skills WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_skill = $stmt->fetch();
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1 style="font-size: 1.8rem;">Skills Matrix</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Quantifying your technical expertise</p>
    </div>
    <?php if ($action == 'list'): ?>
        <a href="skills.php?action=add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Skill
        </a>
    <?php else: ?>
        <a href="skills.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Matrix
        </a>
    <?php endif; ?>
</div>

<?php if ($msg): ?>
    <div
        style="background: rgba(0, 229, 255, 0.1); border: 1px solid var(--primary); color: var(--primary); padding: 15px 20px; border-radius: 12px; margin-bottom: 30px; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-check-circle"></i> <?php echo $msg; ?>
    </div>
<?php endif; ?>

<?php if ($action == 'list'): ?>
    <div class="card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 60px;">Icon</th>
                        <th>Competency</th>
                        <th>Domain</th>
                        <th>Proficiency Level</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($skills as $s): ?>
                        <tr>
                            <td>
                                <div
                                    style="width: 40px; height: 40px; background: rgba(0, 229, 255, 0.05); border-radius: 10px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border);">
                                    <i class="<?php echo $s['icon']; ?>" style="color: var(--primary); font-size: 1.2rem;"></i>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #fff;"><?php echo htmlspecialchars($s['name']); ?></span>
                            </td>
                            <td>
                                <span class="badge"
                                    style="background: rgba(108, 92, 231, 0.1); color: #6c5ce7; border: 1px solid rgba(108, 92, 231, 0.2);">
                                    <?php echo htmlspecialchars($s['category']); ?>
                                </span>
                            </td>
                            <td style="width: 250px;">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div
                                        style="flex: 1; height: 6px; background: var(--darker); border-radius: 3px; overflow: hidden;">
                                        <div
                                            style="width: <?php echo $s['proficiency']; ?>%; height: 100%; background: var(--gradient);">
                                        </div>
                                    </div>
                                    <span
                                        style="font-size: 0.85rem; font-weight: 700; width: 35px; text-align: right;"><?php echo $s['proficiency']; ?>%</span>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                    <a href="skills.php?action=edit&id=<?php echo $s['id']; ?>" class="btn btn-secondary"
                                        style="padding: 8px 12px; font-size: 0.8rem;">
                                        <i class="fas fa-cog"></i>
                                    </a>
                                    <a href="skills.php?action=delete&id=<?php echo $s['id']; ?>" class="btn btn-secondary"
                                        style="padding: 8px 12px; font-size: 0.8rem; color: #ff4d4d;"
                                        onclick="return confirm('Remove this skill?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($action == 'add' || $action == 'edit'): ?>
    <div class="card" style="max-width: 800px;">
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $edit_skill ? $edit_skill['id'] : ''; ?>">

            <div class="grid-2">
                <div class="form-group">
                    <label>Skill Name</label>
                    <input type="text" name="name" class="form-control"
                        value="<?php echo $edit_skill ? htmlspecialchars($edit_skill['name']) : ''; ?>"
                        placeholder="e.g. JavaScript, AWS, React" required>
                </div>
                <div class="form-group">
                    <label>Technology Domain</label>
                    <select name="category" class="form-control" required>
                        <option value="Frontend" <?php echo ($edit_skill && $edit_skill['category'] == 'Frontend') ? 'selected' : ''; ?>>Frontend Engineering</option>
                        <option value="Backend" <?php echo ($edit_skill && $edit_skill['category'] == 'Backend') ? 'selected' : ''; ?>>Backend & APIs</option>
                        <option value="Tools" <?php echo ($edit_skill && $edit_skill['category'] == 'Tools') ? 'selected' : ''; ?>>DevOps & Tools</option>
                    </select>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Icon Identifier (FontAwesome)</label>
                    <input type="text" name="icon" class="form-control"
                        value="<?php echo $edit_skill ? htmlspecialchars($edit_skill['icon']) : ''; ?>"
                        placeholder="fab fa-react">
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 8px;">Use classes from <a
                            href="https://fontawesome.com/icons" target="_blank" style="color: var(--primary);">FontAwesome
                            6.0</a></p>
                </div>
                <div class="form-group">
                    <label>Mastery Level (0-100%)</label>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <input type="range" name="proficiency" min="0" max="100" class="form-control"
                            style="padding: 0; background: transparent; height: 30px;"
                            value="<?php echo $edit_skill ? $edit_skill['proficiency'] : '80'; ?>"
                            oninput="this.nextElementSibling.value = this.value + '%'">
                        <output
                            style="font-weight: 700; width: 45px; color: var(--primary);"><?php echo $edit_skill ? $edit_skill['proficiency'] : '80'; ?>%</output>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                <button type="submit" name="save_skill" class="btn btn-primary" style="padding: 14px 40px;">
                    <?php echo $edit_skill ? 'Update Mastery' : 'Commit Skill'; ?>
                </button>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>