<?php
require_once('../config/database.php');

// Get unique usernames with active tickets
$mongodb_conn = get_mongodb_connection();
$usernames = $mongodb_conn->tickets->distinct('username', ['status' => true]);

// Get tickets for selected username or all active tickets
$filter = ['status' => true];
if (isset($_GET['username']) && !empty($_GET['username'])) {
    $filter['username'] = $_GET['username'];
}
$tickets = executeMongoQuery($filter, ['sort' => ['created_at' => -1]]);
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

        <!-- Username Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="username" class="col-form-label">Filter by Username:</label>
                    </div>
                    <div class="col-auto">
                        <select name="username" id="username" class="form-select" onchange="this.form.submit()">
                            <option value="">All Users</option>
                            <?php foreach ($usernames as $username): ?>
                                <option value="<?php echo htmlspecialchars($username); ?>"
                                    <?php echo (isset($_GET['username']) && $_GET['username'] === $username) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($username); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
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
                                    <small><?php echo $ticket->created_at; ?></small>
                                </div>
                                <p class="mb-1"><?php echo htmlspecialchars($ticket->message); ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small>Comments: <?php echo count($ticket->comments ?? []); ?></small>
                                    <span class="badge bg-success">Active</span>
                                </div>
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