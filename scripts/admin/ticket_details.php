<?php
// MongoDB connection
try {
    $mongoManager = new MongoDB\Driver\Manager("mongodb://127.0.0.1:27017");
    
    if (!isset($_GET['id'])) {
        header('Location: index.php');
        exit;
    }

    // Handle ticket resolution
    if (isset($_POST['resolve_ticket'])) {
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update(
            ['_id' => new MongoDB\BSON\ObjectId($_GET['id'])],
            ['$set' => ['status' => false]]
        );
        $mongoManager->executeBulkWrite('support_system.tickets', $bulk);
        header('Location: index.php');
        exit;
    }

    // Handle new comment submission
    if (isset($_POST['comment']) && !empty($_POST['comment'])) {
        $bulk = new MongoDB\Driver\BulkWrite;
        $comment = [
            'created_at' => new MongoDB\BSON\UTCDateTime(time() * 1000),
            'username' => 'admin',
            'comment' => $_POST['comment']
        ];
        $bulk->update(
            ['_id' => new MongoDB\BSON\ObjectId($_GET['id'])],
            ['$push' => ['comments' => $comment]]
        );
        $mongoManager->executeBulkWrite('support_system.tickets', $bulk);
        header('Location: ticket_details.php?id=' . $_GET['id']);
        exit;
    }

    // Get ticket details
    $query = new MongoDB\Driver\Query(['_id' => new MongoDB\BSON\ObjectId($_GET['id'])]);
    $cursor = $mongoManager->executeQuery('support_system.tickets', $query);
    $ticket = current($cursor->toArray());

    if (!$ticket) {
        die("Ticket not found");
    }

} catch (MongoDB\Driver\Exception\Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Ticket Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Admin Dashboard</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Ticket Details</h1>
            <a href="index.php" class="btn btn-secondary">Back to Tickets</a>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ticket Information</h5>
                    <?php if ($ticket->status): ?>
                    <form method="POST" style="display: inline;">
                        <button type="submit" name="resolve_ticket" class="btn btn-success">Mark as Resolved</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <p><strong>Username:</strong> <?php echo $ticket->username; ?></p>
                <p><strong>Status:</strong> 
                    <?php if ($ticket->status): ?>
                        <span class="badge bg-success">Active</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Resolved</span>
                    <?php endif; ?>
                </p>
                <p><strong>Created At:</strong> <?php echo date('Y-m-d H:i:s', $ticket->created_at->toDateTime()->getTimestamp()); ?></p>
                <p><strong>Issue:</strong></p>
                <p class="border p-3 bg-light"><?php echo $ticket->body; ?></p>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Comments</h5>
            </div>
            <div class="card-body">
                <?php if (isset($ticket->comments)): ?>
                    <?php foreach ($ticket->comments as $comment): ?>
                        <div class="border-bottom mb-3 pb-3">
                            <div class="d-flex justify-content-between">
                                <strong><?php echo $comment->username; ?></strong>
                                <small><?php echo date('Y-m-d H:i:s', $comment->created_at->toDateTime()->getTimestamp()); ?></small>
                            </div>
                            <p class="mt-2 mb-0"><?php echo $comment->comment; ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No comments yet.</p>
                <?php endif; ?>

                <?php if ($ticket->status): ?>
                    <form method="POST" class="mt-4">
                        <div class="mb-3">
                            <label for="comment" class="form-label">Add Comment</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Comment</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 