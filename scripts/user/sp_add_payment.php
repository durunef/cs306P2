<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

$mysql_conn = get_mysql_connection();
$message = '';
$message_type = '';

// Store members data in an array to avoid result set issues
$members = array();
$members_query = "SELECT m.Member_ID, m.Name, mp.Cost 
                 FROM Member m 
                 JOIN Membership_Plan mp ON m.Plan_ID = mp.Plan_ID 
                 ORDER BY m.Name";
$members_result = $mysql_conn->query($members_query);
while ($member = $members_result->fetch_assoc()) {
    $members[] = $member;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = (int)$_POST['member_id'];
    $amount = (float)$_POST['amount'];
    $date = $_POST['date'];
    $payment_method = $_POST['payment_method'];

    try {
        // Call the stored procedure
        $stmt = $mysql_conn->prepare("CALL sp_add_payment(?, ?, ?, ?)");
        $stmt->bind_param("idss", $member_id, $amount, $date, $payment_method);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $message = $row['message'] . " (Payment ID: " . $row['payment_id'] . ")";
            $message_type = "success";
            
            // Only clear form on success
            $_POST = array();
        }
    } catch (mysqli_sql_exception $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = "danger";
    }
}

// Get current date for the date input default value
$current_date = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Payment - Gym Management</title>
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Add Payment</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $message_type; ?>">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="member_id" class="form-label">Select Member</label>
                                <select class="form-control" id="member_id" name="member_id" required onchange="updateAmount()">
                                    <option value="">Select Member</option>
                                    <?php foreach ($members as $member): ?>
                                        <option value="<?php echo $member['Member_ID']; ?>" 
                                                data-cost="<?php echo $member['Cost']; ?>"
                                                <?php echo (isset($_POST['member_id']) && $_POST['member_id'] == $member['Member_ID']) ? 'selected' : ''; ?>>
                                            <?php echo $member['Name']; ?> (Plan Cost: $<?php echo number_format($member['Cost'], 2); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" 
                                       value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : ''; ?>" required>
                                <div class="form-text">Amount must match the member's plan cost.</div>
                            </div>
                            <div class="mb-3">
                                <label for="date" class="form-label">Payment Date</label>
                                <input type="date" class="form-control" id="date" name="date" 
                                       value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : $current_date; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-control" id="payment_method" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="Credit Card" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'Credit Card') ? 'selected' : ''; ?>>Credit Card</option>
                                    <option value="Debit Card" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'Debit Card') ? 'selected' : ''; ?>>Debit Card</option>
                                    <option value="Cash" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'Cash') ? 'selected' : ''; ?>>Cash</option>
                                    <option value="Bank Transfer" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'Bank Transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                                    <option value="PayPal" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'PayPal') ? 'selected' : ''; ?>>PayPal</option>
                                </select>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="index.php" class="btn btn-secondary">Back</a>
                                <button type="submit" class="btn btn-primary">Add Payment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-fill amount when member is selected
        function updateAmount() {
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