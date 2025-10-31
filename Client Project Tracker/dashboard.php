<?php
require __DIR__ . '/db/connect.php';
require_login();

$userId = $_SESSION['user']['id'];

// Handle delete (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $del = (int)$_POST['delete_id'];
    $stmt = $pdo->prepare('DELETE FROM projects WHERE id = ? AND user_id = ?');
    $stmt->execute([$del, $userId]);
    header('Location: dashboard.php');
    exit;
}

// Filters
$order = $_GET['order'] ?? 'deadline';
$dir   = ($_GET['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';
$allowed = ['deadline', 'progress', 'created_at', 'client'];
if (!in_array($order, $allowed, true)) $order = 'deadline';

$stmt = $pdo->prepare("SELECT * FROM projects WHERE user_id = ? ORDER BY $order $dir");
$stmt->execute([$userId]);
$projects = $stmt->fetchAll();

// Simple counters
$total = count($projects);
$active = 0;
$done = 0;
foreach ($projects as $p) {
    ($p['progress'] >= 100) ? $done++ : $active++;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Dashboard • Client Project Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header class="topbar">
        <div>Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?></div>
        <nav>
            <a class="button" href="add_project.php">+ New Project</a>
            <a class="link" href="login.php?logout=1">Logout</a>
        </nav>
    </header>

    <main class="container">
        <section class="grid stats">
            <div class="stat">
                <div class="kpi"><?= $total ?></div>
                <div class="label">Total</div>
            </div>
            <div class="stat">
                <div class="kpi"><?= $active ?></div>
                <div class="label">Active</div>
            </div>
            <div class="stat">
                <div class="kpi"><?= $done ?></div>
                <div class="label">Completed</div>
            </div>
        </section>

        <section class="controls">
            <form method="get">
                <label>Sort by
                    <select name="order">
                        <option value="deadline" <?= $order === 'deadline' ? 'selected' : '' ?>>Deadline</option>
                        <option value="progress" <?= $order === 'progress' ? 'selected' : '' ?>>Progress</option>
                        <option value="client" <?= $order === 'client' ? 'selected' : '' ?>>Client</option>
                        <option value="created_at" <?= $order === 'created_at' ? 'selected' : '' ?>>Created</option>
                    </select>
                </label>
                <label>Direction
                    <select name="dir">
                        <option value="asc" <?= $dir === 'ASC' ? 'selected' : '' ?>>Asc</option>
                        <option value="desc" <?= $dir === 'DESC' ? 'selected' : '' ?>>Desc</option>
                    </select>
                </label>
                <button type="submit">Apply</button>
            </form>
        </section>

        <section class="table-wrap">
            <?php if (!$projects): ?>
                <div class="empty">No projects yet. Create one!</div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Client</th>
                            <th>Consultant</th>
                            <th>Deadline</th>
                            <th>Progress</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['title']) ?></td>
                                <td><?= htmlspecialchars($p['client']) ?></td>
                                <td><?= htmlspecialchars($p['consultant']) ?></td>
                                <td><?= htmlspecialchars($p['deadline']) ?></td>
                                <td style="min-width:160px">
                                    <div class="bar"><span style="width: <?= $p['progress'] ?>%"></span></div>
                                    <small><?= $p['progress'] ?>%</small>
                                </td>
                                <td class="actions">
                                    <a class="button small" href="add_project.php?id=<?= $p['id'] ?>">Edit</a>
                                    <form method="post" onsubmit="return confirm('Delete this project?')" style="display:inline">
                                        <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
                                        <button class="button danger small" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
    <script src="script.js"></script>
</body>

</html>