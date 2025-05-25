<?php
require_once __DIR__ . '/../../config/database.php';

use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;

try {
    $mongo = new Manager("mongodb://127.0.0.1:27017");

    // Get distinct usernames with active tickets
    $filter = ['status' => true];
    $query = new Query($filter);
    $cursor = $mongo->executeQuery('fitness_center.tickets', $query);

    $usernames = [];
    foreach ($cursor as $ticket) {
        if (!in_array($ticket->username, $usernames)) {
            $usernames[] = $ticket->username;
        }
    }

} catch (Exception $e) {
    die("MongoDB connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1>Support Tickets</h1>
        <a href="../index.php" class="btn btn-secondary mb-3">🏠 Home</a>

        <?php if (count($usernames) === 0): ?>
            <div class="alert alert-warning">There are no active tickets.</div>
        <?php else: ?>
            <form method="GET" class="mb-3">
                <label for="username" class="form-label">Select a user:</label>
                <select name="username" id="username" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Choose a user --</option>
                    <?php foreach ($usernames as $uname): ?>
                        <option value="<?= $uname ?>" <?= isset($_GET['username']) && $_GET['username'] === $uname ? 'selected' : '' ?>>
                            <?= htmlspecialchars($uname) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <?php if (isset($_GET['username']) && in_array($_GET['username'], $usernames)): ?>
                <h5 class="mt-4">Tickets for: <?= htmlspecialchars($_GET['username']) ?></h5>
                <ul class="list-group mb-3">
                    <?php
                    $ticket_query = new Query([
                        'username' => $_GET['username'],
                        'status' => true
                    ]);
                    $ticket_cursor = $mongo->executeQuery('fitness_center.tickets', $ticket_query);
                    foreach ($ticket_cursor as $ticket):
                    ?>
                        <li class="list-group-item">
                            <strong><?= htmlspecialchars($ticket->_id) ?></strong> -
                            <?= htmlspecialchars($ticket->message) ?> -
                            <a href="ticket_detail.php?id=<?= $ticket->_id ?>" class="btn btn-sm btn-outline-primary float-end">View</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        <?php endif; ?>

        <a href="create_ticket.php" class="btn btn-success">Create New Ticket</a>
    </div>
</body>
</html>
