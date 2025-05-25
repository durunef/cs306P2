<?php
require_once('../config/database.php');

$message = '';
$error = '';
$ticket = null;

if (!isset($_GET['id'])) {
    header('Location: index.php');
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

// Handle admin actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['comment'])) {
        $comment = "[Admin] " . trim($_POST['comment']);
        
        if (!empty($comment)) {
            try {
                $bulk = new MongoDB\Driver\BulkWrite;
                $bulk->update(
                    ['_id' => new MongoDB\BSON\ObjectId($_GET['id'])],
                    ['$push' => ['comments' => $comment]]
                );
                $mongodb_conn->executeBulkWrite('GymDB.tickets', $bulk);
                $message = "Comment added successfully!";
            } catch (Exception $e) {
                $error = "Error adding comment: " . $e->getMessage();
            }
        }
    } elseif (isset($_POST['resolve'])) {
        try {
            $bulk = new MongoDB\Driver\BulkWrite;
            $bulk->update(
                ['_id' => new MongoDB\BSON\ObjectId($_GET['id'])],
                ['$set' => ['status' => false]]
            );
            $mongodb_conn->executeBulkWrite('GymDB.tickets', $bulk);
            $message = "Ticket marked as resolved!";
            header("refresh:2;url=index.php");
        } catch (Exception $e) {
            $error = "Error resolving ticket: " . $e->getMessage();
        }
    }
    
    if (empty($error)) {
        // Refresh ticket data
        $query = new MongoDB\Driver\Query(['_id' => new MongoDB\BSON\ObjectId($_GET['id'])]);
        $result = executeMongoQuery($query);
        $ticket = current($result);
    }
}
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
            <a class="navbar-brand" href="index.php">Admin Dashboard</a>
            <div class="navbar-nav">
                <a class="nav-link" href="index.php">Tickets</a>
                <a class="nav-link" href="../user/index.php">User View</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($ticket): ?>
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
                        <form method="POST" action="" class="mb-4">
                            <div class="mb-3">
                                <label for="comment" class="form-label">Add Admin Comment</label>
                                <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Comment</button>
                        </form>

                        <form method="POST" action="" onsubmit="return confirm('Are you sure you want to resolve this ticket?');">
                            <input type="hidden" name="resolve" value="1">
                            <button type="submit" class="btn btn-success">Mark as Resolved</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-secondary">Back to Tickets</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 