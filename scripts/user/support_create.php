<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && isset($_POST['message']) && !empty($_POST['username']) && !empty($_POST['message'])) {
        try {
            // Create ticket document with correct schema
            $ticket = [
                'username' => $_POST['username'],
                'message' => $_POST['message'],
                'status' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'comments' => []
            ];

            // Insert into MongoDB
            $result = executeMongoWrite($ticket);

            if ($result->getInsertedId()) {
                $message = "Support ticket created successfully!";
                $message_type = "success";
                $ticket_id = (string)$result->getInsertedId();
                // Clear form
                $_POST = array();
            } else {
                throw new Exception("Failed to create ticket");
            }
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
            $message_type = "danger";
        }
    } else {
        $message = "Error: Please fill in all required fields";
        $message_type = "danger";
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

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php if ($message && $message_type === 'success' && isset($ticket_id)): ?>
                    <div class="alert alert-success">
                        <?php echo $message; ?>
                        <div class="mt-3">
                            <a href="support_view.php?id=<?php echo $ticket_id; ?>" class="btn btn-primary me-2">View Ticket</a>
                            <a href="support_create.php" class="btn btn-secondary me-2">Create Another Ticket</a>
                            <a href="support_index.php" class="btn btn-outline-primary">Return to Tickets List</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Create Support Ticket</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($message): ?>
                                <div class="alert alert-<?php echo $message_type; ?>">
                                    <?php echo $message; ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                           required>
                                </div>

                                <div class="mb-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required><?php 
                                        echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; 
                                    ?></textarea>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">Create Ticket</button>
                                    <a href="support_index.php" class="btn btn-secondary">Back to Support</a>
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