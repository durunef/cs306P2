<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

$message = '';
$message_type = '';
$is_new_ticket = false;

try {
    // Get MongoDB connection
    $mongo_db = get_mongo_db();
    $tickets = $mongo_db->tickets;
    
    // Get ticket ID from URL
    $ticket_id = $_GET['id'] ?? null;
    
    if (!$ticket_id) {
        throw new Exception("No ticket ID provided");
    }
    
    // Convert string ID to MongoDB ObjectId
    $ticket_id = new MongoDB\BSON\ObjectId($ticket_id);
    
    // Get ticket details
    $ticket = $tickets->findOne(['_id' => $ticket_id]);
    
    if (!$ticket) {
        throw new Exception("Ticket not found");
    }

    // Check if this is a new ticket (no comments yet)
    $is_new_ticket = empty($ticket['comments']);
    
    // Handle new comment submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
        $comment = $_POST['comment'];
        
        // Add comment to ticket
        $result = $tickets->updateOne(
            ['_id' => $ticket_id],
            ['$push' => ['comments' => $comment]]
        );
        
        if ($result->getModifiedCount() > 0) {
            $message = "Comment added successfully!";
            $message_type = "success";
            // Refresh ticket data
            $ticket = $tickets->findOne(['_id' => $ticket_id]);
            $is_new_ticket = false;
        } else {
            $message = "Failed to add comment.";
            $message_type = "danger";
        }
    }
    
} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_new_ticket ? 'Ticket Created' : 'View Ticket'; ?> - Fitness Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Fitness Center</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="support.php">View All Tickets</a>
                <a class="nav-link" href="create_ticket.php">Create New Ticket</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                        <div class="mt-3">
                            <a href="support.php" class="btn btn-secondary">Back to Ticket List</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <?php echo $is_new_ticket ? 'Ticket Created Successfully' : 'Ticket Details'; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if ($message): ?>
                                <div class="alert alert-<?php echo $message_type; ?>">
                                    <?php echo $message; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($is_new_ticket): ?>
                                <div class="alert alert-success">
                                    Your ticket has been created successfully! You can view it below or create another ticket.
                                </div>
                            <?php endif; ?>

                            <div class="mb-4">
                                <h6>Ticket Information</h6>
                                <p><strong>Username:</strong> <?php echo htmlspecialchars($ticket['username']); ?></p>
                                <p><strong>Created:</strong> <?php echo $ticket['created_at']; ?></p>
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-<?php echo $ticket['status'] ? 'success' : 'secondary'; ?>">
                                        <?php echo $ticket['status'] ? 'Active' : 'Resolved'; ?>
                                    </span>
                                </p>
                                <p><strong>Message:</strong></p>
                                <div class="border p-3 bg-light">
                                    <?php echo nl2br(htmlspecialchars($ticket['message'])); ?>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6>Comments</h6>
                                <?php if (empty($ticket['comments'])): ?>
                                    <p class="text-muted">No comments yet.</p>
                                <?php else: ?>
                                    <div class="list-group">
                                        <?php foreach ($ticket['comments'] as $comment): ?>
                                            <div class="list-group-item">
                                                <?php echo nl2br(htmlspecialchars($comment)); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="comment" class="form-label">Add Comment</label>
                                    <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">Add Comment</button>
                                    <a href="support.php" class="btn btn-secondary">Back to Ticket List</a>
                                    <?php if ($is_new_ticket): ?>
                                        <a href="create_ticket.php" class="btn btn-success">Create Another Ticket</a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 