<?php
require_once('../config/database.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Gym Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .feature-card {
            border-radius: 8px;
            margin-bottom: 20px;
            transition: transform 0.2s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .author-tag {
            font-size: 0.9em;
            color: #6c757d;
            font-style: italic;
        }
        /* Stored Procedures Colors */
        .procedure-box-1 {
            background-color: #E3F2FD;  /* Light pastel blue */
            border: 1px solid #BBDEFB;
        }
        .procedure-box-2 {
            background-color: #BBDEFB;  /* Darker pastel blue */
            border: 1px solid #90CAF9;
        }
        .btn-procedure {
            background-color: #1565C0;  /* Dark blue */
            border-color: #1565C0;
            color: white;
        }
        .btn-procedure:hover {
            background-color: #0D47A1;
            border-color: #0D47A1;
            color: white;
        }
        /* Triggers Colors */
        .trigger-box-1 {
            background-color: #F3E5F5;  /* Light pastel purple */
            border: 1px solid #E1BEE7;
        }
        .trigger-box-2 {
            background-color: #E1BEE7;  /* Darker pastel purple */
            border: 1px solid #CE93D8;
        }
        .btn-trigger {
            background-color: #6A1B9A;  /* Dark purple */
            border-color: #6A1B9A;
            color: white;
        }
        .btn-trigger:hover {
            background-color: #4A148C;
            border-color: #4A148C;
            color: white;
        }
        /* Support System Colors */
        .support-box {
            background-color: #E8F5E9;  /* Light pastel green */
            border: 1px solid #C8E6C9;
        }
        .btn-support {
            background-color: #2E7D32;  /* Dark green */
            border-color: #2E7D32;
            color: white;
        }
        .btn-support:hover {
            background-color: #1B5E20;
            border-color: #1B5E20;
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <!-- Stored Procedures Section -->
            <div class="col-md-6 mb-4">
                <h3 class="mb-3">Stored Procedures</h3>
                
                <!-- Register Member Procedure -->
                <div class="card feature-card procedure-box-1">
                    <div class="card-body">
                        <h5 class="card-title">Register Member <span class="author-tag">(by Duru)</span></h5>
                        <p class="card-text">Handles new member registration by:</p>
                        <ul>
                            <li>Validating member information</li>
                            <li>Assigning membership plan</li>
                            <li>Creating payment records</li>
                            <li>Generating member ID</li>
                        </ul>
                        <a href="sp_register_member.php" class="btn btn-procedure">Register a Member</a>
                    </div>
                </div>

                <!-- Add Payment Procedure -->
                <div class="card feature-card procedure-box-2">
                    <div class="card-body">
                        <h5 class="card-title">Add Payment <span class="author-tag">(by Zeynep)</span></h5>
                        <p class="card-text">Manages payment processing by:</p>
                        <ul>
                            <li>Recording payment details</li>
                            <li>Updating member payment status</li>
                            <li>Generating payment receipts</li>
                            <li>Tracking payment history</li>
                        </ul>
                        <a href="sp_add_payment.php" class="btn btn-procedure">Add a Payment</a>
                    </div>
                </div>
            </div>

            <!-- Triggers Section -->
            <div class="col-md-6 mb-4">
                <h3 class="mb-3">Triggers</h3>
                
                <!-- Attendance Trigger -->
                <div class="card feature-card trigger-box-1">
                    <div class="card-body">
                        <h5 class="card-title">Attendance Trigger <span class="author-tag">(by Duru)</span></h5>
                        <p class="card-text">Automatically manages class attendance by:</p>
                        <ul>
                            <li>Preventing class overbooking</li>
                            <li>Checking member active status</li>
                            <li>Updating class capacity</li>
                            <li>Recording attendance time</li>
                        </ul>
                        <a href="trigger_attendance.php" class="btn btn-trigger">Record Attendance</a>
                    </div>
                </div>

                <!-- Payment Verification Trigger -->
                <div class="card feature-card trigger-box-2">
                    <div class="card-body">
                        <h5 class="card-title">Payment Verification <span class="author-tag">(by Zeynep)</span></h5>
                        <p class="card-text">Ensures payment integrity by:</p>
                        <ul>
                            <li>Validating payment amounts</li>
                            <li>Checking payment dates</li>
                            <li>Verifying member status</li>
                            <li>Updating payment records</li>
                        </ul>
                        <a href="trigger_payment_check.php" class="btn btn-trigger">Check Payment</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support System Section -->
        <div class="row mt-4 mb-4">
            <div class="col-12">
                <div class="card feature-card support-box">
                    <div class="card-body">
                        <h3 class="card-title">Support Ticket System <span class="author-tag">(MongoDB Integration)</span></h3>
                        <p class="card-text">Our integrated support system provides seamless communication between members and staff:</p>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Features:</h5>
                                <ul>
                                    <li>Real-time ticket tracking</li>
                                    <li>MongoDB-powered storage for flexible data handling</li>
                                    <li>Status tracking for open/resolved issues</li>
                                </ul>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <a href="support_index.php" class="btn btn-support btn-lg">
                                <i class="bi bi-headset"></i> Access Support System
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
