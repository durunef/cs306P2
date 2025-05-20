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
        // Call the stored procedure
        $stmt = $mysql_conn->prepare("CALL sp_register_member(?, ?, ?, ?, ?)");
        $stmt->bind_param('sisii', $name, $age, $gender, $contact_info, $plan_id);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $new_member = $result->fetch_assoc();
            $message = "Member registered successfully! New Member ID: " . $new_member['new_member_id'];
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
    <title>Register New Member - Fitness Center</title>
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
                        <h5 class="card-title mb-0">Register New Member</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6>About the Registration Process</h6>
                            <p class="mb-0">This form uses a stored procedure to register new members. The procedure handles the insertion of member details and returns the newly created member ID.</p>
                        </div>

                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $message_type; ?>">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="age" class="form-label">Age</label>
                                <input type="number" class="form-control" id="age" name="age" 
                                       min="16" max="100" required>
                            </div>

                            <div class="mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select name="gender" id="gender" class="form-select" required>
                                    <option value="">Select gender...</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="contact_info" class="form-label">Contact Information</label>
                                <input type="email" class="form-control" id="contact_info" name="contact_info" 
                                       placeholder="email@example.com" required>
                            </div>

                            <div class="mb-3">
                                <label for="plan_id" class="form-label">Membership Plan</label>
                                <select name="plan_id" id="plan_id" class="form-select" required>
                                    <option value="">Select a plan...</option>
                                    <?php while ($plan = $plans_result->fetch_assoc()): ?>
                                        <option value="<?php echo $plan['Plan_ID']; ?>">
                                            <?php echo htmlspecialchars($plan['Plan_Name']); ?> 
                                            ($<?php echo number_format($plan['Cost'], 2); ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Register Member</button>
                                <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
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