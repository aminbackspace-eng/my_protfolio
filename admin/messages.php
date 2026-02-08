<?php
require_once 'includes/header.php';

if (isset($_GET['delete_id'])) {
    $pdo->prepare("DELETE FROM messages WHERE id = ?")->execute([$_GET['delete_id']]);
    header('Location: messages.php');
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = ?")->execute([$id]);
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE id = ?");
    $stmt->execute([$id]);
    $msg = $stmt->fetch();
}

$messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
?>

<div class="grid-2">
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 25px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0;"><i class="fas fa-inbox" style="margin-right: 10px; color: var(--primary);"></i> Inbound Queries</h3>
            <span class="badge badge-read"><?php echo count($messages); ?> Total</span>
        </div>
        <div style="max-height: 700px; overflow-y: auto;">
            <table style="width:100%;">
                <tbody>
                    <?php foreach ($messages as $m): ?>
                    <tr style="cursor: pointer; <?php echo $m['is_read'] == 0 ? 'background: rgba(0, 229, 255, 0.03);' : ''; ?>" onclick="window.location='messages.php?id=<?php echo $m['id']; ?>'">
                        <td style="padding: 20px 25px;">
                            <div style="display: flex; gap: 15px; align-items: flex-start;">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: <?php echo $m['is_read'] == 1 ? 'var(--darker)' : 'var(--gradient)'; ?>; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700;">
                                    <?php echo strtoupper(substr($m['name'], 0, 1)); ?>
                                </div>
                                <div style="flex: 1;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                        <span style="font-weight: 600; color: <?php echo $m['is_read'] == 0 ? '#fff' : 'var(--text-muted)'; ?>;">
                                            <?php echo htmlspecialchars($m['name']); ?>
                                        </span>
                                        <span style="font-size: 0.75rem; color: var(--text-muted);"><?php echo date('M d', strtotime($m['created_at'])); ?></span>
                                    </div>
                                    <div style="font-size: 0.85rem; color: <?php echo $m['is_read'] == 0 ? 'var(--text-main)' : 'var(--text-muted)'; ?>; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 250px;">
                                        <?php echo htmlspecialchars($m['subject']); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($messages)): ?>
                        <tr><td style="text-align:center; padding: 100px 0; color: var(--text-muted);">Your inbox is currently empty.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card" style="min-height: 500px;">
        <?php if (isset($msg)): ?>
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; padding-bottom: 25px; border-bottom: 1px solid var(--border);">
                <div>
                    <h2 style="margin-bottom: 5px;"><?php echo htmlspecialchars($msg['subject']); ?></h2>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">
                        From: <strong style="color: var(--primary);"><?php echo htmlspecialchars($msg['name']); ?></strong> 
                        &lt;<?php echo htmlspecialchars($msg['email']); ?>&gt;
                    </p>
                </div>
                <div>
                    <button onclick="window.print()" class="btn btn-secondary" style="margin-right: 10px;" title="Print Message">
                        <i class="fas fa-print"></i>
                    </button>
                    <a href="messages.php?delete_id=<?php echo $msg['id']; ?>" class="btn btn-secondary" style="color: #ff4d4d;" onclick="return confirm('Delete this conversation?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </div>
            
            <div style="background: var(--darker); padding: 30px; border-radius: 15px; border: 1px solid var(--border); line-height: 1.8; color: var(--text-main); white-space: pre-wrap; font-size: 1.05rem;">
                <?php echo htmlspecialchars($msg['message']); ?>
            </div>
            
            <div style="margin-top: 40px; display: flex; gap: 15px;">
                <a href="mailto:<?php echo $msg['email']; ?>?subject=Re: <?php echo $msg['subject']; ?>" class="btn btn-primary" style="padding: 14px 30px;">
                    <i class="fas fa-reply"></i> Compose Reply
                </a>
                <span style="color: var(--text-muted); font-size: 0.85rem; align-self: center;">
                    Received on <?php echo date('F d, Y \a\t h:i A', strtotime($msg['created_at'])); ?>
                </span>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: var(--text-muted); text-align: center;">
                <i class="fas fa-envelope-open" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.2;"></i>
                <h3>Select a Message</h3>
                <p>Choose an inquiry from the workspace to view details</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>