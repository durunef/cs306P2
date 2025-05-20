<?php
require_once '../config/database.php';

$mysql_conn = get_mysql_connection();
$mongo_db = get_mongo_db();
$ticket_id = $_GET['id'] ?? null;

if (!$ticket_id) {
    header('Location: index.php');
    exit;
}

$tickets_collection = $mongo_db->tickets;
$ticket = $tickets_collection->findOne(['_id' => new MongoDB\BSON\ObjectId($ticket_id)]);

if (!$ticket) {
    header('Location: index.php');
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['comment'])) {
        // Add new comment
        $comment = [
            'text' => $_POST['comment'],
            'created_at' => date('Y-m-d H:i:s'),
            'is_admin' => true
        ];
        
        $tickets_collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($ticket_id)],
            ['$push' => ['comments' => $comment]]
        );
    } elseif (isset($_POST['resolve'])) {
        // Mark ticket as resolved
        $tickets_collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($ticket_id)],
            ['$set' => ['status' => 'resolved']]
        );
    }
    
    // Refresh the page
    header('Location: ticket_detail.php?id=' . $ticket_id);
    exit;
}

// Get member name
$member_query = "SELECT name FROM members WHERE member_id = ?";
$stmt = $mysql_conn->prepare($member_query);
$stmt->bind_param('i', $ticket['member_id']);
$stmt->execute();
$member_result = $stmt->get_result();
$member = $member_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Details - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Fitness Center Admin</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Ticket Details</h5>
                        <span class="badge bg-<?php echo $ticket['status'] === 'open' ? 'success' : 'secondary'; ?>">
                            <?php echo ucfirst($ticket['status']); ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <h4><?php echo htmlspecialchars($ticket['subject']); ?></h4>
                        <p class="text-muted">
                            By <?php echo htmlspecialchars($member['name']); ?> on 
                            <?php echo date('F j, Y, g:i a', strtotime($ticket['created_at'])); ?>
                        </p>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Comments</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($ticket['comments'])): ?>
                            <p class="text-muted">No comments yet.</p>
                        <?php else: ?>
                            <?php foreach ($ticket['comments'] as $comment): ?>
                                <div class="d-flex mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="avatar bg-<?php echo $comment['is_admin'] ? 'primary' : 'secondary'; ?> text-white rounded-circle p-2">
                                            <?php echo $comment['is_admin'] ? 'A' : 'U'; ?>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <?php echo $comment['is_admin'] ? 'Admin' : 'User'; ?>
                                            </h6>
                                            <small class="text-muted">
                                                <?php echo date('M d, Y g:i a', strtotime($comment['created_at'])); ?>
                                            </small>
                                        </div>
                                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($comment['text'])); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($ticket['status'] === 'open'): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Add Comment</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <textarea class="form-control" name="comment" rows="3" required></textarea>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Add Comment</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Resolve Ticket</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="" onsubmit="return confirm('Are you sure you want to resolve this ticket?');">
                                <input type="hidden" name="resolve" value="1">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success">Mark as Resolved</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="index.php" class="btn btn-secondary">Back to Tickets</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 