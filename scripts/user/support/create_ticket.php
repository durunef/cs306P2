<?php
require_once __DIR__ . '/../../config/database.php';

use MongoDB\Driver\Manager;
use MongoDB\Driver\BulkWrite;

$success = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $message = trim($_POST['message']);

    if ($username && $message) {
        try {
            $mongo = new Manager("mongodb://127.0.0.1:27017");
            $bulk = new BulkWrite();

            $ticket = [
                'username' => $username,
                'message' => $message,
                'created_at' => date('Y-m-d H:i:s'),
                'status' => true,
                'comments' => []
            ];

            $bulk->insert($ticket);
            $mongo->executeBulkWrite('fitness_center.tickets', $bulk);

            $success = "✅ Ticket created successfully.";
        } catch (Exception $e) {
            $error = "❌ Failed to create ticket: " . $e->getMessage();
        }
    } else {
        $error = "⚠️ All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Support Ticket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h1>Create Support Ticket</h1>

    <div class="mb-3">
        <a href="ticket_list.php" class="btn btn-secondary">🔙 Back to Ticket List</a>
        <a href="../index.php" class="btn btn-outline-dark ms-2">🏠 Dashboard</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label for="username" class="form-label">Username:</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="message" class="form-label">Ticket Message:</label>
            <textarea name="message" id="message" class="form-control" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">📩 Submit Ticket</button>
    </form>
</div>
</body>
</html>
