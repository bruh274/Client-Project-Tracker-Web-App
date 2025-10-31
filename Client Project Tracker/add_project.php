<?php
require __DIR__ . '/db/connect.php';
require_login();

$userId = $_SESSION['user']['id'];
$isEdit = isset($_GET['id']);
$project = [
    'title' => '',
    'client' => '',
    'consultant' => '',
    'deadline' => '',
    'progress' => 0,
    'notes' => ''
];

// Load for edit
if ($isEdit) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $userId]);
    $project = $stmt->fetch();
    if (!$project) {
        header('Location: dashboard.php');
        exit;
    }
}

// Handle submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $client = trim($_POST['client'] ?? '');
    $consultant = trim($_POST['consultant'] ?? '');
    $deadline = $_POST['deadline'] ?? '';
    $progress = max(0, min(100, (int)($_POST['progress'] ?? 0)));
    $notes = trim($_POST['notes'] ?? '');

    if ($title && $client && $consultant && $deadline) {
        if ($isEdit) {
            $stmt = $pdo->prepare('UPDATE projects SET title=?, client=?, consultant=?, deadline=?, progress=?, notes=? WHERE id=? AND user_id=?');
            $stmt->execute([$title, $client, $consultant, $deadline, $progress, $notes, $project['id'], $userId]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO projects (user_id, title, client, consultant, deadline, progress, notes) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$userId, $title, $client, $consultant, $deadline, $progress, $notes]);
        }
        header('Location: dashboard.php');
        exit;
    } else {
        $err = 'Please fill all required fields.';
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?= $isEdit ? 'Edit' : 'Add' ?> Project • Client Project Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header class="topbar">
        <a class="link" href="dashboard.php">← Back</a>
    </header>

    <main class="container">
        <h2><?= $isEdit ? 'Edit Project' : 'Add Project' ?></h2>
        <?php if (!empty($err)): ?><div class="notice error"><?= htmlspecialchars($err) ?></div><?php endif; ?>
        <form method="post" class="form">
            <label>Title* <input name="title" required value="<?= htmlspecialchars($project['title']) ?>"></label>
            <label>Client* <input name="client" required value="<?= htmlspecialchars($project['client']) ?>"></label>
            <label>Consultant* <input name="consultant" required value="<?= htmlspecialchars($project['consultant']) ?>"></label>
            <label>Deadline* <input type="date" name="deadline" required value="<?= htmlspecialchars($project['deadline']) ?>"></label>
            <label>Progress (0–100) <input type="number" name="progress" min="0" max="100" value="<?= (int)$project['progress'] ?>"></label>
            <label>Notes <textarea name="notes" rows="5"><?= htmlspecialchars($project['notes']) ?></textarea></label>
            <button type="submit"><?= $isEdit ? 'Update' : 'Create' ?></button>
        </form>
    </main>
</body>

</html>