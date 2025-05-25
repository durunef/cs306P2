<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

$mysql_conn = get_mysql_connection();
$message = '';
$message_type = '';

// Get current date as default
$selected_date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');

// Get available classes with attendance count for selected date
function getClassesWithAttendance($conn, $date) {
    $query = "SELECT c.Class_ID, c.Class_Name as Name, c.Capacity, 
        (SELECT COUNT(*) FROM Attendance a 
         WHERE a.Class_ID = c.Class_ID 
         AND a.Date = ? 
         AND a.Status = 'Attended') as current_attendance
        FROM Class c
        ORDER BY c.Class_Name";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $date);
    $stmt->execute();
    return $stmt->get_result();
}

// Handle test submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['date_change'])) {
    // Only process if this is not just a date change
    if (isset($_POST['member_id']) && isset($_POST['class_id']) && isset($_POST['status']) && isset($_POST['date'])) {
        $class_id = (int)$_POST['class_id'];
        $member_id = (int)$_POST['member_id'];
        $status = $_POST['status'];
        $date = $_POST['date'];

        // Validate Member_ID exists
        $check_member = $mysql_conn->prepare("SELECT COUNT(*) as count FROM Member WHERE Member_ID = ?");
        $check_member->bind_param('i', $member_id);
        $check_member->execute();
        $member_exists = $check_member->get_result()->fetch_assoc()['count'];

        // Validate Class_ID exists
        $check_class = $mysql_conn->prepare("SELECT COUNT(*) as count FROM Class WHERE Class_ID = ?");
        $check_class->bind_param('i', $class_id);
        $check_class->execute();
        $class_exists = $check_class->get_result()->fetch_assoc()['count'];

        if (!$member_exists) {
            $message = "Error: Selected member does not exist.";
            $message_type = "danger";
        } else if (!$class_exists) {
            $message = "Error: Selected class does not exist.";
            $message_type = "danger";
        } else {
            try {
                $insert_query = "INSERT INTO Attendance (Member_ID, Class_ID, Date, Status) VALUES (?, ?, ?, ?)";
                $stmt = $mysql_conn->prepare($insert_query);
                $stmt->bind_param('iiss', $member_id, $class_id, $date, $status);
                
                if ($stmt->execute()) {
                    $message = "Successfully added attendance record!";
                    $message_type = "success";
                }
            } catch (Exception $e) {
                $message = "Error: " . $e->getMessage();
                $message_type = "danger";
            }
        }
    }
}

// Get available members for testing
$members_query = "SELECT Member_ID, Name FROM Member ORDER BY Name";
$members_result = $mysql_conn->query($members_query);

// Get classes with attendance for the selected date
$classes_result = getClassesWithAttendance($mysql_conn, $selected_date);

// Store the current selections
$selected_class = isset($_POST['class_id']) ? (int)$_POST['class_id'] : '';
$selected_member = isset($_POST['member_id']) ? (int)$_POST['member_id'] : '';
$selected_status = isset($_POST['status']) ? $_POST['status'] : 'Attended';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Attendance Triggers - Gym Management</title>
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
                        <h5 class="card-title mb-0">Test Attendance Triggers</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6>About the Triggers</h6>
                            <p class="mb-1">1. Overbooking Prevention: Prevents classes from being overbooked by checking against class capacity.</p>
                            <p class="mb-0">2. Duplicate Prevention: Prevents the same member from being marked twice for the same class on the same date.</p>
                        </div>

                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $message_type; ?>">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" id="attendanceForm">
                            <!-- Date Selection (Moved to top) -->
                            <div class="mb-3">
                                <label for="date" class="form-label">Select Date</label>
                                <input type="date" class="form-control" id="date" name="date" 
                                       value="<?php echo $selected_date; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="class_id" class="form-label">Select Class</label>
                                <select name="class_id" id="class_id" class="form-select" required>
                                    <option value="">Select a class...</option>
                                    <?php while ($class = $classes_result->fetch_assoc()): ?>
                                        <option value="<?php echo $class['Class_ID']; ?>" 
                                                <?php echo ($selected_class == $class['Class_ID']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($class['Name']); ?> 
                                            (Capacity: <?php echo $class['Capacity']; ?>, 
                                            Current Attendance: <?php echo $class['current_attendance']; ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="member_id" class="form-label">Select Member</label>
                                <select name="member_id" id="member_id" class="form-select" required>
                                    <option value="">Select a member...</option>
                                    <?php while ($member = $members_result->fetch_assoc()): ?>
                                        <option value="<?php echo $member['Member_ID']; ?>"
                                                <?php echo ($selected_member == $member['Member_ID']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($member['Name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="Attended" <?php echo ($selected_status == 'Attended') ? 'selected' : ''; ?>>Attended</option>
                                    <option value="Missed" <?php echo ($selected_status == 'Missed') ? 'selected' : ''; ?>>Missed</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Add Attendance</button>
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
        // Preserve selected values after form submission
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('attendanceForm');
            const dateInput = document.getElementById('date');

            // Only submit form automatically when date changes
            dateInput.addEventListener('change', function() {
                // Create a hidden input to indicate this is a date change
                const dateChangeInput = document.createElement('input');
                dateChangeInput.type = 'hidden';
                dateChangeInput.name = 'date_change';
                dateChangeInput.value = '1';
                form.appendChild(dateChangeInput);
                
                form.submit();
            });
        });
    </script>
</body>
</html> 