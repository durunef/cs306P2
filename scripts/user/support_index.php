<?php
require_once('../config/database.php');

// Get unique usernames with any tickets (both active and resolved)
$mongodb_conn = get_mongodb_connection();
$usernames = $mongodb_conn->tickets->distinct('username');

// Get tickets for selected username or all tickets
$username_filter = isset($_GET['username']) && !empty($_GET['username']) ? ['username' => $_GET['username']] : [];

// Get active tickets
$active_filter = array_merge(['status' => true], $username_filter);
$active_tickets = executeMongoQuery($active_filter, ['sort' => ['created_at' => -1]]);

// Get resolved tickets
$resolved_filter = array_merge(['status' => false], $username_filter);
$resolved_tickets = executeMongoQuery($resolved_filter, ['sort' => ['created_at' => -1]]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support System - Gym Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .ticket-resolved {
            background-color: #F5F5F5;
            border-color: #E0E0E0;
        }
        .badge-resolved {
            background-color: #78909C !important;
        }
        /* Active Tickets Section */
        .active-tickets-header {
            background-color: #E8F5E9;
            border-bottom: 1px solid #C8E6C9;
            color: #2E7D32;
        }
        .active-tickets-card {
            border-color: #C8E6C9;
        }
        .active-ticket-item {
            border-color: #C8E6C9;
            transition: all 0.3s ease;
        }
        .active-ticket-item:hover {
            background-color: #F1F8F1;
        }
        .badge-active {
            background-color: #2E7D32 !important;
        }
        /* Resolved Tickets Section */
        .resolved-tickets-header {
            background-color: #ECEFF1;
            border-bottom: 1px solid #CFD8DC;
            color: #455A64;
        }
        .resolved-tickets-card {
            border-color: #CFD8DC;
        }
        .resolved-ticket-item {
            background-color: #F5F5F5;
            border-color: #CFD8DC;
            transition: all 0.3s ease;
        }
        .resolved-ticket-item:hover {
            background-color: #ECEFF1;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

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

        <!-- Active Tickets -->
        <div class="card mb-4 active-tickets-card">
            <div class="card-header active-tickets-header">
                <h3 class="mb-0">Active Tickets</h3>
            </div>
            <div class="card-body">
                <?php if (empty($active_tickets)): ?>
                    <p class="text-muted">No active tickets found.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($active_tickets as $ticket): ?>
                            <a href="support_view.php?id=<?php echo $ticket->_id; ?>" 
                               class="list-group-item list-group-item-action active-ticket-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1"><?php echo htmlspecialchars($ticket->username); ?></h5>
                                    <small><?php echo $ticket->created_at; ?></small>
                                </div>
                                <p class="mb-1"><?php echo htmlspecialchars($ticket->message); ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small>Comments: <?php echo count($ticket->comments ?? []); ?></small>
                                    <span class="badge badge-active">Active</span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Resolved Tickets -->
        <div class="card resolved-tickets-card">
            <div class="card-header resolved-tickets-header">
                <h3 class="mb-0">Resolved Tickets</h3>
            </div>
            <div class="card-body">
                <?php if (empty($resolved_tickets)): ?>
                    <p class="text-muted">No resolved tickets found.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($resolved_tickets as $ticket): ?>
                            <a href="support_view.php?id=<?php echo $ticket->_id; ?>" 
                               class="list-group-item list-group-item-action resolved-ticket-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1"><?php echo htmlspecialchars($ticket->username); ?></h5>
                                    <small><?php echo $ticket->created_at; ?></small>
                                </div>
                                <p class="mb-1"><?php echo htmlspecialchars($ticket->message); ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small>Comments: <?php echo count($ticket->comments ?? []); ?></small>
                                    <span class="badge badge-resolved">Resolved</span>
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