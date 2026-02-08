<?php
require_once 'includes/header.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_project'])) {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $technologies = trim($_POST['technologies']);
    $live_link = trim($_POST['live_link']);
    $github_link = trim($_POST['github_link']);
    $sort_order = isset($_POST['sort_order']) ? (int) $_POST['sort_order'] : 0;

    // Handle Image Upload
    $image_path = $_POST['current_image'];
    if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] == 0) {
        $target_dir = "../images/projects/";
        if (!is_dir($target_dir))
            mkdir($target_dir, 0755, true);

        $file_ext = strtolower(pathinfo($_FILES['project_image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($file_ext, $allowed)) {
            $new_name = time() . '_' . uniqid() . '.' . $file_ext;
            $target_file = $target_dir . $new_name;

            if (move_uploaded_file($_FILES['project_image']['tmp_name'], $target_file)) {
                // Delete old image if exists and different
                if (!empty($_POST['current_image']) && file_exists("../" . $_POST['current_image'])) {
                    unlink("../" . $_POST['current_image']);
                }
                $image_path = "images/projects/" . $new_name;
            }
        }
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE projects SET title=?, category=?, description=?, image_path=?, technologies=?, live_link=?, github_link=?, sort_order=? WHERE id=?");
        $stmt->execute([$title, $category, $description, $image_path, $technologies, $live_link, $github_link, $sort_order, $id]);
        $success = "Project synchronized successfully.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO projects (title, category, description, image_path, technologies, live_link, github_link, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $category, $description, $image_path, $technologies, $live_link, $github_link, $sort_order]);
        $success = "Project launched successfully.";
    }
    header("Location: projects.php?success=" . urlencode($success));
    exit;
}

if ($action == 'delete' && isset($_GET['id'])) {
    // Get image path first to delete file
    $stmt = $pdo->prepare("SELECT image_path FROM projects WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $p = $stmt->fetch();
    if ($p && !empty($p['image_path']) && file_exists("../" . $p['image_path'])) {
        unlink("../" . $p['image_path']);
    }

    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    header("Location: projects.php?success=" . urlencode("Project archived successfully."));
    exit;
}

if (isset($_GET['success']))
    $msg = $_GET['success'];

$projects = $pdo->query("SELECT * FROM projects ORDER BY sort_order ASC, created_at DESC")->fetchAll();

$edit_project = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_project = $stmt->fetch();
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1 style="font-size: 1.8rem;">
            <?php echo $action == 'list' ? 'Project Portfolio' : ($action == 'edit' ? 'Edit Project' : 'Launch New Project'); ?>
        </h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Manage your featured works and case studies</p>
    </div>
    <?php if ($action == 'list'): ?>
        <a href="projects.php?action=add" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Project
        </a>
    <?php else: ?>
        <a href="projects.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Fleet
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
                        <th style="width: 80px;">Preview</th>
                        <th>Project Identity</th>
                        <th>Category</th>
                        <th>Stack</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $p): ?>
                        <tr>
                            <td>
                                <img src="../<?php echo $p['image_path']; ?>"
                                    style="width:60px; height:45px; object-fit:cover; border-radius:8px; border: 1px solid var(--border);">
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column;">
                                    <span
                                        style="font-weight: 600; color: #fff;"><?php echo htmlspecialchars($p['title']); ?></span>
                                    <span
                                        style="font-size: 0.8rem; color: var(--text-muted);"><?php echo substr(htmlspecialchars($p['description']), 0, 40) . '...'; ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-read"><?php echo htmlspecialchars($p['category']); ?></span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                    <?php
                                    $techs = explode(',', $p['technologies']);
                                    foreach (array_slice($techs, 0, 3) as $t): ?>
                                        <span
                                            style="font-size: 0.7rem; background: rgba(255,255,255,0.05); padding: 2px 8px; border-radius: 4px;"><?php echo trim($t); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                    <a href="projects.php?action=edit&id=<?php echo $p['id']; ?>" class="btn btn-secondary"
                                        style="padding: 8px 12px; font-size: 0.8rem;" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="projects.php?action=delete&id=<?php echo $p['id']; ?>" class="btn btn-secondary"
                                        style="padding: 8px 12px; font-size: 0.8rem; color: #ff4d4d;"
                                        onclick="return confirm('Archive this project?')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($projects)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 50px; color: var(--text-muted);">No projects in
                                your portfolio yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($action == 'add' || $action == 'edit'): ?>
    <div class="card">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $edit_project ? $edit_project['id'] : ''; ?>">
            <input type="hidden" name="current_image"
                value="<?php echo $edit_project ? $edit_project['image_path'] : ''; ?>">

            <div class="grid-2">
                <div class="form-group">
                    <label>Project Name</label>
                    <input type="text" name="title" class="form-control"
                        value="<?php echo $edit_project ? htmlspecialchars($edit_project['title']) : ''; ?>"
                        placeholder="Enter project name" required>
                </div>
                <div class="form-group">
                    <label>Industry / Category</label>
                    <input type="text" name="category" class="form-control"
                        value="<?php echo $edit_project ? htmlspecialchars($edit_project['category']) : ''; ?>"
                        placeholder="e.g. Fintech, E-commerce">
                </div>
            </div>

            <div class="form-group">
                <label>Display Order (Priority)</label>
                <input type="number" name="sort_order" class="form-control"
                    value="<?php echo $edit_project ? (int) $edit_project['sort_order'] : '0'; ?>"
                    placeholder="0 = Highest Priority">
            </div>

            <div class="form-group">
                <label>Project Narrative (Description)</label>
                <textarea name="description" class="form-control" rows="5"
                    placeholder="What was the goal of this project?"><?php echo $edit_project ? htmlspecialchars($edit_project['description']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label>Technical Stack (Comma separated)</label>
                <input type="text" name="technologies" class="form-control"
                    value="<?php echo $edit_project ? htmlspecialchars($edit_project['technologies']) : ''; ?>"
                    placeholder="React, Node.js, MongoDB">
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label><i class="fas fa-link"></i> Deployment URL</label>
                    <input type="url" name="live_link" class="form-control"
                        value="<?php echo $edit_project ? htmlspecialchars($edit_project['live_link']) : ''; ?>"
                        placeholder="https://demo.com">
                </div>
                <div class="form-group">
                    <label><i class="fab fa-github"></i> Repository Link</label>
                    <input type="url" name="github_link" class="form-control"
                        value="<?php echo $edit_project ? htmlspecialchars($edit_project['github_link']) : ''; ?>"
                        placeholder="https://github.com/your-repo">
                </div>
            </div>

            <div class="form-group">
                <label>Featured Visual (Thumbnail)</label>
                <div style="display: flex; gap: 20px; align-items: flex-start;">
                    <?php if ($edit_project && $edit_project['image_path']): ?>
                        <div style="flex-shrink: 0;">
                            <img src="../<?php echo $edit_project['image_path']; ?>"
                                style="width:160px; height:100px; object-fit:cover; border-radius:12px; border: 2px solid var(--border);">
                            <p style="font-size: 0.75rem; color: var(--text-muted); text-align: center; margin-top: 5px;">
                                Current Cover</p>
                        </div>
                    <?php endif; ?>
                    <div style="flex: 1;">
                        <input type="file" name="project_image" class="form-control" accept="image/*"
                            style="padding: 10px;">
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 10px;">Best resolution:
                            1200x800px. Supports JPG, PNG, WebP.</p>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 20px;">
                <button type="submit" name="save_project" class="btn btn-primary" style="padding: 14px 40px;">
                    <?php echo $edit_project ? 'Update Project' : 'Commit Project'; ?>
                </button>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>