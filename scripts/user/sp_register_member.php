<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

$mysql_conn = get_mysql_connection();
$message = '';
$message_type = '';

// Get available membership plans
$plans_query = "SELECT Plan_ID, Plan_Name, Cost FROM Membership_Plan ORDER BY Cost";
$plans_result = $mysql_conn->query($plans_query);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $age = (int)$_POST['age'];
    $gender = $_POST['gender'];
    $contact_info = $_POST['contact_info'];
    $plan_id = (int)$_POST['plan_id'];

    try {
        // Call the stored procedure with your implementation
        $stmt = $mysql_conn->prepare("CALL sp_register_member(?, ?, ?, ?, ?)");
        $stmt->bind_param("sissi", $name, $age, $gender, $contact_info, $plan_id);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $message = isset($row['message']) ? $row['message'] . " (Member ID: " . $row['new_member_id'] . ")" : "Member registered successfully!";
            $message_type = "success";
            
            // Clear form data on success
            $_POST = array();
        }
    } catch (mysqli_sql_exception $e) {
        if (strpos($e->getMessage(), 'A member with this email address already exists') !== false) {
            $message = "❌ This email address is already registered. Please use a different email.";
        } else {
            $message = "Error: " . $e->getMessage();
        }
        $message_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register New Member - Gym Management</title>
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
                        <h3>Register New Member</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $message_type; ?>">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="age" class="form-label">Age</label>
                                <input type="number" class="form-control" id="age" name="age" min="16" max="100"
                                       value="<?php echo isset($_POST['age']) ? htmlspecialchars($_POST['age']) : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-control" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                    <option value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="contact_info" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="contact_info" name="contact_info"
                                       value="<?php echo isset($_POST['contact_info']) ? htmlspecialchars($_POST['contact_info']) : ''; ?>" required>
                                <div class="form-text">This email must be unique and will be used as the member's contact information.</div>
                            </div>
                            <div class="mb-3">
                                <label for="plan_id" class="form-label">Membership Plan</label>
                                <select class="form-control" id="plan_id" name="plan_id" required>
                                    <option value="">Select Plan</option>
                                    <?php while ($plan = $plans_result->fetch_assoc()): ?>
                                        <option value="<?php echo $plan['Plan_ID']; ?>" 
                                                <?php echo (isset($_POST['plan_id']) && $_POST['plan_id'] == $plan['Plan_ID']) ? 'selected' : ''; ?>>
                                            <?php echo $plan['Plan_Name']; ?> - $<?php echo number_format($plan['Cost'], 2); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="index.php" class="btn btn-secondary">Back</a>
                                <button type="submit" class="btn btn-primary">Register Member</button>
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