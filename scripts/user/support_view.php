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
    $mongodb_conn = get_mongodb_connection();
    $collection = $mongodb_conn->tickets;
    
    $filter = ['_id' => new MongoDB\BSON\ObjectId($_GET['id'])];
    $tickets = executeMongoQuery($filter);
    $ticket = !empty($tickets) ? $tickets[0] : null;

    if (!$ticket) {
        throw new Exception("Ticket not found");
    }
} catch (Exception $e) {
    $error = "Error loading ticket: " . $e->getMessage();
}

// Handle new comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    try {
        $comment = trim($_POST['comment']);
        if (!empty($comment)) {
            $commentData = [
                'created_at' => date('Y-m-d H:i:s'),
                'username' => $ticket->username,
                'comment' => $comment
            ];
            
            $result = $collection->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($_GET['id'])],
                ['$push' => ['comments' => $commentData]]
            );

            if ($result->getModifiedCount() > 0) {
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            } else {
                throw new Exception("Failed to add comment");
            }
        }
    } catch (Exception $e) {
        $error = "Error adding comment: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Ticket - Support System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .comment-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 0.9em;
        }
        .comment-username {
            font-weight: bold;
        }
        .comment-time {
            color: #6c757d;
        }
        .comment-content {
            margin-top: 5px;
        }
        .admin-comment {
            background-color: #f8f9fa;
            border-left: 4px solid #0d6efd;
        }
        .user-comment {
            border-left: 4px solid #198754;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Support System</a>
            <div class="navbar-nav">
                <a class="nav-link" href="index.php">Home</a>
                <a class="nav-link" href="support_index.php">Support</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif ($ticket): ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Ticket #<?php echo substr($ticket->_id, -6); ?></h3>
                        <span class="badge bg-<?php echo $ticket->status ? 'success' : 'secondary'; ?> fs-6">
                            <?php echo $ticket->status ? 'Active' : 'Resolved'; ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>From: <?php echo htmlspecialchars($ticket->username); ?></h5>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="text-muted mb-0">Created: <?php echo htmlspecialchars($ticket->created_at); ?></p>
                        </div>
                    </div>
                    <div class="ticket-message p-3 bg-light rounded">
                        <p class="lead mb-0"><?php echo htmlspecialchars($ticket->message); ?></p>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Comments</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($ticket->comments)): ?>
                        <p class="text-muted">No comments yet.</p>
                    <?php else: ?>
                        <div class="list-group mb-4">
                            <?php foreach ($ticket->comments as $comment): ?>
                                <?php 
                                    $isAdmin = false;
                                    $username = '';
                                    $commentText = '';
                                    $created_at = '';
                                    
                                    if (is_object($comment) || is_array($comment)) {
                                        // New comment format
                                        $isAdmin = ($comment->username === 'admin');
                                        $username = $comment->username;
                                        $commentText = $comment->comment;
                                        $created_at = $comment->created_at;
                                    } else {
                                        // Legacy comment format
                                        $isAdmin = (strpos($comment, '[ADMIN]') === 0);
                                        $username = $isAdmin ? 'admin' : $ticket->username;
                                        $commentText = $isAdmin ? substr($comment, 7) : $comment;
                                        $created_at = isset($ticket->created_at) ? $ticket->created_at : '';
                                    }
                                ?>
                                <div class="list-group-item <?php echo $isAdmin ? 'admin-comment' : 'user-comment'; ?>">
                                    <div class="comment-header">
                                        <span class="comment-username">
                                            <?php echo $isAdmin ? 'Admin' : htmlspecialchars($username); ?>
                                        </span>
                                        <?php if ($created_at): ?>
                                            <span class="comment-time">
                                                <?php echo htmlspecialchars($created_at); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="comment-content">
                                        <?php echo htmlspecialchars($commentText); ?>
                                    </div>
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