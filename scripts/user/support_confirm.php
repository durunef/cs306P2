<?php
require_once '../config/database.php';

$mongo_db = get_mongo_db();
$ticket_id = $_GET['id'] ?? null;

if (!$ticket_id) {
    header('Location: support.php');
    exit;
}

$tickets_collection = $mongo_db->tickets;
$ticket = $tickets_collection->findOne(['_id' => new MongoDB\BSON\ObjectId($ticket_id)]);

if (!$ticket) {
    header('Location: support.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Created - Fitness Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Fitness Center</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                            </svg>
                        </div>
                        
                        <h2 class="card-title mb-4">Ticket Created Successfully!</h2>
                        
                        <div class="card mb-4">
                            <div class="card-body text-start">
                                <h5 class="card-title"><?php echo htmlspecialchars($ticket['subject']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($ticket['description']); ?></p>
                                <p class="text-muted mb-0">
                                    <small>Created on: <?php echo date('F j, Y, g:i a', strtotime($ticket['created_at'])); ?></small>
                                </p>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="support_detail.php?id=<?php echo $ticket_id; ?>" class="btn btn-primary">View Ticket Details</a>
                            <a href="support.php" class="btn btn-secondary">Back to Support Tickets</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 