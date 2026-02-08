<?php
require_once 'includes/header.php';

// Fetch stats
$projects_count = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$skills_count = $pdo->query("SELECT COUNT(*) FROM skills")->fetchColumn();
$messages_count = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
$unread_messages = $pdo->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn();

// Fetch latest messages
$latest_messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>

<div class="stats-grid">
    <div class="stat-card">
        <i class="fas fa-rocket"></i>
        <h3><?php echo $projects_count; ?></h3>
        <p>Total Projects</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-bolt"></i>
        <h3><?php echo $skills_count; ?></h3>
        <p>Skills Matrix</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-comment-dots" style="color: #ff4d4d;"></i>
        <h3 style="color: #ff4d4d;"><?php echo $unread_messages; ?></h3>
        <p>Unread Message<?php echo $unread_messages != 1 ? 's' : ''; ?></p>
    </div>
    <div class="stat-card">
        <i class="fas fa-envelope-open-text"></i>
        <h3><?php echo $messages_count; ?></h3>
        <p>Total Inquiries</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1.2fr; gap: 30px;">
    <div class="card">
        <div class="card-header">
            <h3>Recent Inquiries</h3>
            <a href="messages.php" class="btn btn-secondary" style="padding: 8px 16px; font-size: 0.85rem;">View All</a>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Sender</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latest_messages as $msg): ?>
                        <tr>
                            <td>
                                <div style="display:flex; flex-direction:column;">
                                    <span style="font-weight:600;"><?php echo htmlspecialchars($msg['name']); ?></span>
                                    <span
                                        style="font-size:0.8rem; color:var(--text-muted);"><?php echo htmlspecialchars($msg['email']); ?></span>
                                </div>
                            </td>
                            <td>
                                <?php if ($msg['is_read'] == 0): ?>
                                    <span class="badge badge-unread"
                                        style="margin-right: 5px; font-size: 0.65rem; padding: 2px 8px;">New</span>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($msg['subject']); ?>
                            </td>
                            <td style="color:var(--text-muted);">
                                <?php echo date('M d, Y', strtotime($msg['created_at'])); ?>
                            </td>
                            <td>
                                <a href="messages.php?id=<?php echo $msg['id']; ?>" class="btn btn-secondary"
                                    style="padding: 6px 12px; font-size: 0.8rem;">
                                    Manage
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($latest_messages)): ?>
                        <tr>
                            <td colspan="4" style="text-align:center; padding: 40px; color:var(--text-muted);">No messages
                                found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Quick Actions</h3>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <a href="projects.php?action=add" class="btn btn-primary" style="justify-content: center;">
                <i class="fas fa-plus"></i> Launch New Project
            </a>
            <a href="skills.php?action=add" class="btn btn-secondary" style="justify-content: center;">
                <i class="fas fa-bolt"></i> Add Tech Skill
            </a>
            <a href="settings.php" class="btn btn-secondary" style="justify-content: center;">
                <i class="fas fa-sliders-h"></i> Portfolio Config
            </a>

            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border);">
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 15px;">System Health</p>
                <div
                    style="background: var(--darker); height: 8px; border-radius: 4px; overflow: hidden; margin-bottom: 10px;">
                    <div style="background: var(--primary); width: 85%; height: 100%;"></div>
                </div>
                <div style="display:flex; justify-content:space-between; font-size: 0.8rem;">
                    <span>Storage Usage</span>
                    <span>85%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>