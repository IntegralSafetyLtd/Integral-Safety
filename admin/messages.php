<?php
/**
 * Contact Messages Management
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    dbExecute("DELETE FROM contact_submissions WHERE id = ?", [$_GET['delete']]);
    $_SESSION['flash_message'] = 'Message deleted.';
    $_SESSION['flash_type'] = 'success';
    header('Location: /admin/messages.php');
    exit;
}

// Handle mark as read
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    dbExecute("UPDATE contact_submissions SET is_read = 1 WHERE id = ?", [$_GET['view']]);
}

$viewId = $_GET['view'] ?? null;
$viewMessage = $viewId ? dbFetchOne("SELECT * FROM contact_submissions WHERE id = ?", [$viewId]) : null;

$messages = dbFetchAll("SELECT * FROM contact_submissions ORDER BY submitted_at DESC");

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($viewMessage): ?>
<div class="mb-4">
    <a href="/admin/messages.php" class="text-orange-500">&larr; Back to Messages</a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Message from <?= e($viewMessage['name']) ?></h1>
            <p class="text-gray-500"><?= formatDate($viewMessage['submitted_at'], 'j F Y \a\t g:ia') ?></p>
        </div>
        <a href="/admin/messages.php?delete=<?= $viewMessage['id'] ?>" class="text-red-500 hover:text-red-600" data-confirm="Delete this message?">Delete</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <label class="block text-gray-500 text-sm">Name</label>
            <p class="text-gray-800 font-medium"><?= e($viewMessage['name']) ?></p>
        </div>
        <div>
            <label class="block text-gray-500 text-sm">Email</label>
            <p class="text-gray-800"><a href="mailto:<?= e($viewMessage['email']) ?>" class="text-orange-500"><?= e($viewMessage['email']) ?></a></p>
        </div>
        <div>
            <label class="block text-gray-500 text-sm">Phone</label>
            <p class="text-gray-800"><?= e($viewMessage['phone'] ?: 'Not provided') ?></p>
        </div>
        <div>
            <label class="block text-gray-500 text-sm">Company</label>
            <p class="text-gray-800"><?= e($viewMessage['company'] ?: 'Not provided') ?></p>
        </div>
    </div>

    <div>
        <label class="block text-gray-500 text-sm mb-2">Message</label>
        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-gray-800 whitespace-pre-wrap"><?= e($viewMessage['message']) ?></p>
        </div>
    </div>

    <div class="mt-6 pt-6 border-t flex space-x-4">
        <a href="mailto:<?= e($viewMessage['email']) ?>?subject=Re: Your inquiry to Integral Safety"
           class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
            Reply via Email
        </a>
        <?php if ($viewMessage['phone']): ?>
        <a href="tel:<?= e($viewMessage['phone']) ?>" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Call <?= e($viewMessage['phone']) ?>
        </a>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-800">Contact Messages</h1>
    <p class="text-gray-600">Messages submitted through the contact form</p>
</div>

<?php if (empty($messages)): ?>
<div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
    No messages yet.
</div>
<?php else: ?>
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">From</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Message</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php foreach ($messages as $msg): ?>
            <tr class="<?= $msg['is_read'] ? '' : 'bg-orange-50' ?>">
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900 <?= $msg['is_read'] ? '' : 'font-bold' ?>"><?= e($msg['name']) ?></div>
                    <div class="text-sm text-gray-500"><?= e($msg['email']) ?></div>
                </td>
                <td class="px-6 py-4 text-gray-600">
                    <?= e(truncate($msg['message'], 60)) ?>
                </td>
                <td class="px-6 py-4 text-gray-500 text-sm">
                    <?= formatDate($msg['submitted_at'], 'j M Y') ?>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="/admin/messages.php?view=<?= $msg['id'] ?>" class="text-orange-500 hover:text-orange-600">View</a>
                    <a href="/admin/messages.php?delete=<?= $msg['id'] ?>" class="text-red-500 hover:text-red-600" data-confirm="Delete?">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
