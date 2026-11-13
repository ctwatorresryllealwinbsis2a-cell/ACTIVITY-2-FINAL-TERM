
<?php
require_once 'config.php';
require_admin();

$users = $pdo->query(
    "SELECT id, username, status, role, created_at
     FROM users
     ORDER BY id DESC"
)->fetchAll();

$page_title = "User Management";
include 'includes/header.php';
?>

<div class="panel">
    <div class="panel-heading">
        <h3>Registered Users</h3>
        <span class="badge approved">
            <?= count($users) ?> Users
        </span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Date Registered</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= e($u['id']) ?></td>
                    <td><?= e($u['username']) ?></td>
                    <td><?= e(ucfirst($u['role'])) ?></td>
                    <td>
                        <span class="badge <?= $u['status'] === 'approved' ? 'approved' : 'warning' ?>">
                            <?= e(ucfirst($u['status'])) ?>
                        </span>
                    </td>
                    <td><?= e($u['created_at']) ?></td>
                    <td class="actions">
                        <?php if ($u['id'] != $_SESSION['user_id']): ?>

                            <?php if ($u['status'] === 'pending'): ?>
                                <form method="POST" action="approveuser.php">
                                    <input type="hidden" name="csrf_token"
                                           value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id"
                                           value="<?= (int)$u['id'] ?>">
                                    <button class="btn small edit">
                                        Approve
                                    </button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="disableuser.php"
                                      onsubmit="return confirm('Disable this account?')">
                                    <input type="hidden" name="csrf_token"
                                           value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id"
                                           value="<?= (int)$u['id'] ?>">
                                    <button class="btn small danger">
                                        Disable
                                    </button>
                                </form>
                            <?php endif; ?>

                        <?php else: ?>
                            <span class="muted">Current account</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (!$users): ?>
                <tr><td colspan="6">No users registered.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

