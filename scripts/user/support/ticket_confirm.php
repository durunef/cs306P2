<?php
$success = isset($_GET['status']) && $_GET['status'] === 'success';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1>🎫 Ticket Confirmation</h1>

        <?php if ($success): ?>
            <div class="alert alert-success mt-3">
                ✅ Your support ticket has been successfully submitted.
            </div>
        <?php else: ?>
            <div class="alert alert-danger mt-3">
                ❌ There was an issue creating your ticket. Please try again.
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="create_ticket.php" class="btn btn-outline-primary me-2">📝 Create Another Ticket</a>
            <a href="ticket_list.php" class="btn btn-secondary me-2">📋 View Ticket List</a>
            <a href="../index.php" class="btn btn-dark">🏠 Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
