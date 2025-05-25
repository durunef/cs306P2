<?php
// MongoDB connection check
try {
    $mongoManager = new MongoDB\Driver\Manager("mongodb://127.0.0.1:27017");
    $mongoManager->executeCommand("admin", new MongoDB\Driver\Command(["ping" => 1]));
    $mongo_status = "<div class='alert alert-success'>✅ MongoDB connection successful.</div>";
} catch (MongoDB\Driver\Exception\Exception $e) {
    $mongo_status = "<div class='alert alert-danger'>❌ MongoDB connection failed: " . $e->getMessage() . "</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Center - User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Fitness Center</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Welcome to Fitness Center</h1>
        <?php echo $mongo_status; ?>

        <!-- Trigger Tests -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Trigger Tests</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <a href="test_overbooking.php" class="text-decoration-none">Test Overbooking Prevention</a>
                            </li>
                            <li class="list-group-item">
                                <a href="test_payment_verification.php" class="text-decoration-none">Test Payment Verification</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Stored Procedures -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Stored Procedures</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <a href="register_member.php" class="text-decoration-none">Register New Member</a>
                            </li>
                            <li class="list-group-item">
                                <a href="add_payment.php" class="text-decoration-none">Add Payment</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support Tickets -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Support Tickets</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <a href="support/ticket_list.php" class="text-decoration-none">🎫 View Support Tickets</a>
                            </li>
                            <li class="list-group-item">
                                <a href="support/create_ticket.php" class="text-decoration-none">📝 Create New Ticket</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
