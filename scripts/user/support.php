<?php
require_once '../config/database.php';

$mysql_conn = get_mysql_connection();
$mongo_db = get_mongo_db();

// Get all members for dropdown
$members_query = "SELECT member_id, name FROM members ORDER BY name";
$members_result = $mysql_conn->query($members_query);

// Get selected member's tickets
$selected_member = isset($_GET['member_id']) ? (int)$_GET['member_id'] : null;
$tickets = [];

if ($selected_member) {
    $tickets_collection = $mongo_db->tickets;
    $tickets = $tickets_collection->find(
        ['member_id' => $selected_member],
        ['sort' => ['created_at' => -1]]
    )->toArray();
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
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Support Tickets</h1>
            <a href="support_create.php" class="btn btn-primary">Create New Ticket</a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label for="member_id" class="form-label">Select Member</label>
                        <select name="member_id" id="member_id" class="form-select" onchange="this.form.submit()">
                            <option value="">Select a member...</option>
                            <?php while ($member = $members_result->fetch_assoc()): ?>
                                <option value="<?php echo $member['member_id']; ?>" 
                                    <?php echo $selected_member == $member['member_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($member['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($selected_member): ?>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Active Tickets</h5>
                    <?php if (empty($tickets)): ?>
                        <p class="text-muted">No active tickets found.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($tickets as $ticket): ?>
                                <a href="support_detail.php?id=<?php echo $ticket['_id']; ?>" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($ticket['subject']); ?></h6>
                                        <small class="text-muted">
                                            <?php echo date('M d, Y', strtotime($ticket['created_at'])); ?>
                                        </small>
                                    </div>
                                    <p class="mb-1"><?php echo htmlspecialchars($ticket['description']); ?></p>
                                    <small class="text-<?php echo $ticket['status'] === 'open' ? 'success' : 'secondary'; ?>">
                                        Status: <?php echo ucfirst($ticket['status']); ?>
                                    </small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 