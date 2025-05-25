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
    <title>Admin Dashboard - Gym Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Admin Dashboard</a>
            <div class="navbar-nav">
                <a class="nav-link active" href="index.php">Tickets</a>
                <a class="nav-link" href="../user/index.php">User View</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h3>Active Support Tickets</h3>
            </div>
            <div class="card-body">
                <?php if (empty($tickets)): ?>
                    <p class="text-muted">No active tickets found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Message</th>
                                    <th>Created</th>
                                    <th>Comments</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tickets as $ticket): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ticket->username); ?></td>
                                        <td><?php echo htmlspecialchars(substr($ticket->message, 0, 50)) . '...'; ?></td>
                                        <td><?php echo date('Y-m-d H:i', strtotime($ticket->created_at)); ?></td>
                                        <td><?php echo count($ticket->comments); ?></td>
                                        <td>
                                            <a href="ticket_detail.php?id=<?php echo $ticket->_id; ?>" 
                                               class="btn btn-primary btn-sm">View Details</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 