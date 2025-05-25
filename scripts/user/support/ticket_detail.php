<?php
require_once __DIR__ . '/../../config/database.php';

use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use MongoDB\Driver\BulkWrite;
use MongoDB\BSON\ObjectId;

$ticket = null;
$commentError = null;

// Ensure ticket ID is provided
if (!isset($_GET['id'])) {
    die("❌ Ticket ID not provided.");
}

$ticketId = $_GET['id'];

try {
    $mongo = new Manager("mongodb://127.0.0.1:27017");

    // Fetch ticket by ID
    $query = new Query(['_id' => new ObjectId($ticketId)]);
    $cursor = $mongo->executeQuery('fitness_center.tickets', $query);
    $ticket = current($cursor->toArray());

    if (!$ticket) {
        throw new Exception("Ticket not found.");
    }

    // Handle comment submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $comment = trim($_POST['comment']);
        if ($comment) {
            $bulk = new BulkWrite();
            $bulk->update(
                ['_id' => new ObjectId($ticketId)],
                ['$push' => ['comments' => $comment]]
            );
            $mongo->executeBulkWrite('fitness_center.tickets', $bulk);
            header("Location: ticket_detail.php?id=$ticketId");
            exit;
        } else {
            $commentError = "Comment cannot be empty.";
        }
    }

} catch (Exception $e) {
    die("❌ Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket Detail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>🎫 Ticket Details</h2>
    <div class="mb-3">
        <a href="ticket_list.php" class="btn btn-secondary">← Back to Ticket List</a>
        <a href="../index.php" class="btn btn-dark">🏠 Back to Dashboard</a>
    </div>

    <?php if ($ticket): ?>
        <div class="card mb-4">
            <div class="card-header">
                <strong><?= htmlspecialchars($ticket->username) ?></strong> – <?= $ticket->created_at ?>
            </div>
            <div class="card-body">
                <p><strong>Message:</strong> <?= htmlspecialchars($ticket->message) ?></p>
                <p><strong>Status:</strong> <?= $ticket->status ? '✅ Active' : '❌ Closed' ?></p>

                <hr>
                <h5>💬 Comments</h5>
                <?php if (!empty($ticket->comments)): ?>
                    <ul class="list-group">
                        <?php foreach ($ticket->comments as $c): ?>
                            <li class="list-group-item"><?= htmlspecialchars($c) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">No comments yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label for="comment" class="form-label">Add a Comment:</label>
                <textarea name="comment" id="comment" class="form-control" rows="3" required></textarea>
            </div>
            <?php if ($commentError): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($commentError) ?></div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary">Submit Comment</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
