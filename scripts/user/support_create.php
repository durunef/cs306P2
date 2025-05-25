<?php
require_once('../config/database.php');

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $ticket_message = trim($_POST['message']);
    
    if (empty($username) || empty($ticket_message)) {
        $error = "Please fill in all fields.";
    } else {
        // Create new ticket document
        $ticket = [
            'username' => $username,
            'message' => $ticket_message,
            'created_at' => date('Y-m-d H:i:s'),
            'status' => true,
            'comments' => []
        ];
        
        // Insert ticket into MongoDB
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->insert($ticket);
        
        try {
            $mongodb_conn->executeBulkWrite('GymDB.tickets', $bulk);
            $message = "Ticket created successfully!";
            // Redirect after 2 seconds
            header("refresh:2;url=support_index.php");
        } catch (MongoDB\Driver\Exception\Exception $e) {
            $error = "Error creating ticket: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Support Ticket - Gym Management</title>
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Create Support Ticket</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-success"><?php echo $message; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="support_index.php" class="btn btn-secondary">Back to Tickets</a>
                                <button type="submit" class="btn btn-primary">Create Ticket</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 