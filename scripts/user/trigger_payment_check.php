<?php
require_once '../config/database.php';

$mysql_conn = get_mysql_connection();
$message = '';
$message_type = '';

// Get members with their plan costs
$members_query = "SELECT m.Member_ID, m.Name, mp.Plan_ID, mp.Cost 
                 FROM Member m 
                 JOIN Membership_Plan mp ON m.Plan_ID = mp.Plan_ID 
                 ORDER BY m.Name";
$members_result = $mysql_conn->query($members_query);

// Store members data in an array to avoid result set issues
$members = array();
while ($member = $members_result->fetch_assoc()) {
    $members[] = $member;
}

// Store the current selections
$selected_member = isset($_POST['member_id']) ? (int)$_POST['member_id'] : '';
$selected_amount = isset($_POST['amount']) ? (float)$_POST['amount'] : '';
$selected_date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');
$selected_payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';

// Get the selected member's plan cost and name
$selected_member_cost = 0;
$selected_member_name = '';
foreach ($members as $member) {
    if ($member['Member_ID'] == $selected_member) {
        $selected_member_cost = $member['Cost'];
        $selected_member_name = $member['Name'];
        break;
    }
}

// Handle test submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['member_id']) && isset($_POST['amount']) && isset($_POST['date']) && isset($_POST['payment_method'])) {
        $member_id = (int)$_POST['member_id'];
        $amount = (float)$_POST['amount'];
        $payment_method = $_POST['payment_method'];
        $date = $_POST['date'];

        try {
            $insert_query = "INSERT INTO Payment (Member_ID, Amount, Date, Payment_Method) VALUES (?, ?, ?, ?)";
            $stmt = $mysql_conn->prepare($insert_query);
            $stmt->bind_param('idss', $member_id, $amount, $date, $payment_method);
            
            if ($stmt->execute()) {
                $message = "Successfully added payment record!";
                $message_type = "success";
                // Only clear form on success
                $selected_member = '';
                $selected_amount = '';
                $selected_date = date('Y-m-d');
                $selected_payment_method = '';
            }
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Payment amount does not match') !== false) {
                $difference = abs($selected_member_cost - $amount);
                if ($amount > $selected_member_cost) {
                    $message = "Error: For member {$selected_member_name}, the expected payment amount is \${$selected_member_cost}. " .
                              "You've entered \${$amount}, which is \${$difference} more than required.";
                } else {
                    $message = "Error: For member {$selected_member_name}, the expected payment amount is \${$selected_member_cost}. " .
                              "You've entered \${$amount}, which is \${$difference} less than required.";
                }
            } else {
                $message = "Error: " . $e->getMessage();
            }
            $message_type = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Payment Verification - Gym Management</title>
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

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Test Payment Verification</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6>About the Trigger</h6>
                            <p class="mb-0">This trigger ensures that payment amounts match the member's plan cost. If the payment amount doesn't match the plan cost exactly, the payment will be rejected.</p>
                        </div>

                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $message_type; ?>">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" id="paymentForm">
                            <div class="mb-3">
                                <label for="member_id" class="form-label">Select Member</label>
                                <select name="member_id" id="member_id" class="form-select" required onchange="updateExpectedAmount()">
                                    <option value="">Select a member...</option>
                                    <?php foreach ($members as $member): ?>
                                        <option value="<?php echo $member['Member_ID']; ?>" 
                                                data-cost="<?php echo $member['Cost']; ?>"
                                                <?php echo ($selected_member == $member['Member_ID']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($member['Name']); ?> 
                                            (Plan Cost: $<?php echo number_format($member['Cost'], 2); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="amount" class="form-label">Payment Amount</label>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" 
                                       value="<?php echo $selected_amount; ?>" required>
                                <?php if ($selected_member_cost > 0): ?>
                                    <div class="form-text">Expected amount: $<?php echo number_format($selected_member_cost, 2); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="date" class="form-label">Payment Date</label>
                                <input type="date" class="form-control" id="date" name="date" 
                                       value="<?php echo $selected_date; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select name="payment_method" id="payment_method" class="form-select" required>
                                    <option value="">Select payment method...</option>
                                    <option value="Credit Card" <?php echo ($selected_payment_method == 'Credit Card') ? 'selected' : ''; ?>>Credit Card</option>
                                    <option value="Debit Card" <?php echo ($selected_payment_method == 'Debit Card') ? 'selected' : ''; ?>>Debit Card</option>
                                    <option value="Cash" <?php echo ($selected_payment_method == 'Cash') ? 'selected' : ''; ?>>Cash</option>
                                    <option value="Bank Transfer" <?php echo ($selected_payment_method == 'Bank Transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                                    <option value="PayPal" <?php echo ($selected_payment_method == 'PayPal') ? 'selected' : ''; ?>>PayPal</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Process Payment</button>
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
        function updateExpectedAmount() {
            const memberSelect = document.getElementById('member_id');
            const amountInput = document.getElementById('amount');
            const selectedOption = memberSelect.options[memberSelect.selectedIndex];
            
            if (selectedOption.value) {
                const cost = selectedOption.getAttribute('data-cost');
                amountInput.value = cost;
            } else {
                amountInput.value = '';
            }
        }
    </script>
</body>
</html> 