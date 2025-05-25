<?php
require_once('../config/database.php');

// Get selected username from dropdown if any
$selected_username = isset($_GET['username']) ? $_GET['username'] : '';

// Get all active tickets
try {
    // Base filter for active tickets
    $filter = ['status' => true];
    
    // Add username filter if selected
    if (!empty($selected_username)) {
        $filter['username'] = $selected_username;
    }
    
    $options = [
        'sort' => ['created_at' => -1] // Sort by creation date, newest first
    ];
    $tickets = executeMongoQuery($filter, $options);

    // Get unique usernames for dropdown
    $all_usernames = executeMongoQuery(
        ['status' => true],
        ['projection' => ['username' => 1]]
    );
    $unique_usernames = array_unique(array_map(function($ticket) {
        return $ticket->username;
    }, $all_usernames));
    sort($unique_usernames);

} catch (Exception $e) {
    $error = "Error loading tickets: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Support Tickets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Admin Dashboard</a>
            <div class="navbar-nav">
                <a class="nav-link active" href="index.php">Support Tickets</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Active Support Tickets</h2>
        </div>

        <!-- Username Filter Dropdown -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="" class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="username" class="col-form-label">Filter by Username:</label>
                    </div>
                    <div class="col-auto">
                        <select name="username" id="username" class="form-select" onchange="this.form.submit()">
                            <option value="">All Users</option>
                            <?php foreach ($unique_usernames as $username): ?>
                                <option value="<?php echo htmlspecialchars($username); ?>"
                                    <?php echo $selected_username === $username ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($username); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if (!empty($selected_username)): ?>
                        <div class="col-auto">
                            <a href="index.php" class="btn btn-secondary">Clear Filter</a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php else: ?>
            <?php if (empty($tickets)): ?>
                <div class="alert alert-info">
                    <?php echo !empty($selected_username) ? 
                        "No active tickets found for user: " . htmlspecialchars($selected_username) :
                        "No active tickets found."; ?>
                </div>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($tickets as $ticket): ?>
                        <a href="ticket_detail.php?id=<?php echo $ticket->_id; ?>" 
                           class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">Ticket from: <?php echo htmlspecialchars($ticket->username); ?></h5>
                                <small><?php echo htmlspecialchars($ticket->created_at); ?></small>
                            </div>
                            <p class="mb-1"><?php echo htmlspecialchars(substr($ticket->message, 0, 150)) . '...'; ?></p>
                            <small class="text-muted">
                                <?php echo count($ticket->comments); ?> comment(s)
                            </small>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 