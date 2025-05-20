<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

$mysql_conn = get_mysql_connection();
$message = '';
$message_type = '';

// Get members with their plan costs
$members_query = "SELECT m.Member_ID, m.Name, mp.Cost FROM Member m JOIN Membership_Plan mp ON m.Plan_ID = mp.Plan_ID ORDER BY m.Name";
$members_result = $mysql_conn->query($members_query);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = (int)$_POST['member_id'];
    $amount = (float)$_POST['amount'];
    $date = $_POST['date'];
    $payment_method = $_POST['payment_method'];

    try {
        // Call the stored procedure
        $stmt = $mysql_conn->prepare("CALL sp_add_payment(?, ?, ?, ?)");
        $stmt->bind_param('idss', $member_id, $amount, $date, $payment_method);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $payment = $result->fetch_assoc();
            $message = "Payment recorded successfully! Payment ID: " . $payment['payment_id'];
            $message_type = "success";
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Payment - Fitness Center</title>
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
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Add Payment</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6>About the Payment Process</h6>
                            <p class="mb-0">This form uses a stored procedure to record payments. The procedure handles the insertion of payment details and returns the payment ID and confirmation message.</p>
                        </div>

                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $message_type; ?>">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="member_id" class="form-label">Select Member</label>
                                <select name="member_id" id="member_id" class="form-select" required>
                                    <option value="">Select a member...</option>
                                    <?php while ($member = $members_result->fetch_assoc()): ?>
                                        <option value="<?php echo $member['Member_ID']; ?>" 
                                                data-plan-cost="<?php echo $member['Cost']; ?>">
                                            <?php echo htmlspecialchars($member['Name']); ?> 
                                            (Plan Cost: $<?php echo number_format($member['Cost'], 2); ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="amount" class="form-label">Payment Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="amount" name="amount" 
                                           step="0.01" min="0" required>
                                </div>
                                <div class="form-text">Enter the payment amount to be recorded.</div>
                            </div>

                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select name="payment_method" id="payment_method" class="form-select" required>
                                    <option value="">Select payment method...</option>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="Debit Card">Debit Card</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="date" class="form-label">Payment Date</label>
                                <input type="date" class="form-control" id="date" name="date" 
                                       value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Record Payment</button>
                                <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add client-side validation to show the expected amount
        document.getElementById('member_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const planCost = selectedOption.getAttribute('data-plan-cost');
            const amountInput = document.getElementById('amount');
            
            if (planCost) {
                amountInput.placeholder = `Expected amount: $${parseFloat(planCost).toFixed(2)}`;
            } else {
                amountInput.placeholder = '';
            }
        });
    </script>
</body>
</html> 