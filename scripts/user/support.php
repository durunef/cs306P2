<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

try {
    // Get MongoDB connection
    $mongo_db = get_mongo_db();
    $tickets = $mongo_db->tickets;

    // Get unique usernames with active tickets
    $active_users = $tickets->distinct('username', ['status' => true]);

    // If a username is selected, get their tickets
    $selected_user = isset($_GET['username']) ? $_GET['username'] : null;
    $user_tickets = [];
    
    if ($selected_user) {
        $user_tickets = $tickets->find(
            ['username' => $selected_user],
            ['sort' => ['created_at' => -1]]
        )->toArray();
    }

} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Tickets - Fitness Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Fitness Center</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="create_ticket.php">Create New Ticket</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Support Tickets</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="GET" class="mb-4">
                            <div class="mb-3">
                                <label for="username" class="form-label">Select Username</label>
                                <select name="username" id="username" class="form-select" onchange="this.form.submit()">
                                    <option value="">Select a username...</option>
                                    <?php foreach ($active_users as $user): ?>
                                        <option value="<?php echo htmlspecialchars($user); ?>" 
                                                <?php echo $selected_user === $user ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($user); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>

                        <?php if ($selected_user): ?>
                            <?php if (empty($user_tickets)): ?>
                                <div class="alert alert-info">
                                    No active tickets found for this user.
                                </div>
                            <?php else: ?>
                                <div class="list-group">
                                    <?php foreach ($user_tickets as $ticket): ?>
                                        <a href="view_ticket.php?id=<?php echo $ticket['_id']; ?>" 
                                           class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Ticket #<?php echo $ticket['_id']; ?></h6>
                                                <small class="text-<?php echo $ticket['status'] ? 'success' : 'secondary'; ?>">
                                                    <?php echo $ticket['status'] ? 'Active' : 'Resolved'; ?>
                                                </small>
                                            </div>
                                            <p class="mb-1"><?php echo htmlspecialchars(substr($ticket['message'], 0, 100)) . '...'; ?></p>
                                            <small>Created: <?php echo $ticket['created_at']; ?></small>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                Please select a username to view their tickets.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 