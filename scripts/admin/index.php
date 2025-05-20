<?php
require_once '../config/database.php';

$mongo_db = get_mongo_db();
$mysql_conn = get_mysql_connection();

// Get all active tickets
$tickets_collection = $mongo_db->tickets;
$tickets = $tickets_collection->find(
    ['status' => 'open'],
    ['sort' => ['created_at' => -1]]
)->toArray();

// Get member names for display
$member_names = [];
foreach ($tickets as $ticket) {
    if (!isset($member_names[$ticket['member_id']])) {
        $member_query = "SELECT name FROM members WHERE member_id = ?";
        $stmt = $mysql_conn->prepare($member_query);
        $stmt->bind_param('i', $ticket['member_id']);
        $stmt->execute();
        $member_result = $stmt->get_result();
        $member = $member_result->fetch_assoc();
        $member_names[$ticket['member_id']] = $member['name'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Fitness Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Fitness Center Admin</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Active Support Tickets</h1>
        </div>

        <?php if (empty($tickets)): ?>
            <div class="alert alert-info">
                No active tickets found.
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Subject</th>
                                    <th>Member</th>
                                    <th>Created</th>
                                    <th>Comments</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tickets as $ticket): ?>
                                    <tr>
                                        <td><?php echo substr($ticket['_id'], 0, 8); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['subject']); ?></td>
                                        <td><?php echo htmlspecialchars($member_names[$ticket['member_id']]); ?></td>
                                        <td><?php echo date('M d, Y g:i a', strtotime($ticket['created_at'])); ?></td>
                                        <td><?php echo count($ticket['comments'] ?? []); ?></td>
                                        <td>
                                            <a href="ticket_detail.php?id=<?php echo $ticket['_id']; ?>" 
                                               class="btn btn-sm btn-primary">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 