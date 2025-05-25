<?php
require_once('../config/database.php');

// Get all active tickets
$query = new MongoDB\Driver\Query(['status' => true], ['sort' => ['created_at' => -1]]);
$tickets = executeMongoQuery($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support System - Gym Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Gym Management</a>
            <div class="navbar-nav">
                <a class="nav-link" href="index.php">Home</a>
                <a class="nav-link active" href="support_index.php">Support</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Support System</h1>
            <a href="support_create.php" class="btn btn-primary">Create New Ticket</a>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Active Tickets</h3>
            </div>
            <div class="card-body">
                <?php if (empty($tickets)): ?>
                    <p class="text-muted">No active tickets found.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($tickets as $ticket): ?>
                            <a href="support_view.php?id=<?php echo $ticket->_id; ?>" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1"><?php echo htmlspecialchars($ticket->username); ?></h5>
                                    <small><?php echo date('Y-m-d H:i', strtotime($ticket->created_at)); ?></small>
                                </div>
                                <p class="mb-1"><?php echo htmlspecialchars($ticket->message); ?></p>
                                <small>Comments: <?php echo count($ticket->comments); ?></small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 