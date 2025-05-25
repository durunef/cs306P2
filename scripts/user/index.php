<?php
require_once('../config/database.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Gym Management</a>
            <div class="navbar-nav">
                <a class="nav-link" href="index.php">Home</a>
                <a class="nav-link" href="dashboard.php">Tables</a>
                <a class="nav-link" href="support_index.php">Support</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-4">Gym Management System</h1>
        
        <div class="row">
            <!-- Stored Procedures -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h3>Stored Procedures</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <h5>Register Member</h5>
                                <p>Add new members to the system</p>
                                <a href="sp_register_member.php" class="btn btn-primary">Go to Registration</a>
                            </li>
                            <li class="list-group-item">
                                <h5>Add Payment</h5>
                                <p>Process member payments</p>
                                <a href="sp_add_payment.php" class="btn btn-primary">Go to Payments</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Triggers -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h3>Triggers</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <h5>Attendance Trigger</h5>
                                <p>Test class capacity limits</p>
                                <a href="trigger_attendance.php" class="btn btn-primary">Test Attendance</a>
                            </li>
                            <li class="list-group-item">
                                <h5>Payment Verification</h5>
                                <p>Test payment amount verification</p>
                                <a href="trigger_payment_check.php" class="btn btn-primary">Test Payment</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Support System -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h3>Support System</h3>
                    </div>
                    <div class="card-body">
                        <p>Need help? Access our support system to create or view tickets.</p>
                        <a href="support_index.php" class="btn btn-success">Go to Support</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
