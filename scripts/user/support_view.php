<?php
require_once('../config/database.php');

$message = '';
$error = '';
$ticket = null;

if (!isset($_GET['id'])) {
    header('Location: support_index.php');
    exit;
}

// Get ticket details
try {
    $query = new MongoDB\Driver\Query(['_id' => new MongoDB\BSON\ObjectId($_GET['id'])]);
    $result = executeMongoQuery($query);
    $ticket = current($result);
} catch (Exception $e) {
    $error = "Error loading ticket: " . $e->getMessage();
}

// Handle new comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    
    if (!empty($comment)) {
        try {
            $bulk = new MongoDB\Driver\BulkWrite;
            $bulk->update(
                ['_id' => new MongoDB\BSON\ObjectId($_GET['id'])],
                ['$push' => ['comments' => $comment]]
            );
            $mongodb_conn->executeBulkWrite('GymDB.tickets', $bulk);
            $message = "Comment added successfully!";
            // Refresh page to show new comment
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        } catch (Exception $e) {
            $error = "Error adding comment: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Ticket - Gym Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Gym Management</a>
            <div class="navbar-nav">
                <a class="nav-link" href="index.php">Home</a>
                <a class="nav-link" href="support_index.php">Support</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif ($ticket): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>Ticket Details</h3>
                        <span class="badge bg-<?php echo $ticket->status ? 'success' : 'secondary'; ?>">
                            <?php echo $ticket->status ? 'Active' : 'Resolved'; ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <h5>From: <?php echo htmlspecialchars($ticket->username); ?></h5>
                    <p class="text-muted">Created: <?php echo date('Y-m-d H:i', strtotime($ticket->created_at)); ?></p>
                    <p class="lead"><?php echo htmlspecialchars($ticket->message); ?></p>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Comments</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($ticket->comments)): ?>
                        <p class="text-muted">No comments yet.</p>
                    <?php else: ?>
                        <div class="list-group mb-4">
                            <?php foreach ($ticket->comments as $comment): ?>
                                <div class="list-group-item">
                                    <?php echo htmlspecialchars($comment); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($ticket->status): ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="comment" class="form-label">Add Comment</label>
                                <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Comment</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="support_index.php" class="btn btn-secondary">Back to Tickets</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 