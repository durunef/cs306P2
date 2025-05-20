<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

$mysql_conn = get_mysql_connection();
$message = '';
$message_type = '';

// Get available classes for testing
$classes_query = "SELECT c.Class_ID, c.Class_Name as Name, c.Capacity, 
    (SELECT COUNT(*) FROM Attendance a WHERE a.Class_ID = c.Class_ID AND a.Date = CURDATE() AND a.Status = 'Attended') as current_attendance
    FROM Class c
    ORDER BY c.Class_Name";
$classes_result = $mysql_conn->query($classes_query);

// Handle test submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = (int)$_POST['class_id'];
    $member_id = (int)$_POST['member_id'];
    $status = $_POST['status'];
    $date = $_POST['date'];

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

// Get available members for testing
$members_query = "SELECT Member_ID, Name FROM Member ORDER BY Name";
$members_result = $mysql_conn->query($members_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Overbooking Prevention - Fitness Center</title>
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
                        <h5 class="card-title mb-0">Test Overbooking Prevention</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6>About the Trigger</h6>
                            <p class="mb-0">This trigger prevents classes from being overbooked by checking the number of attendees against the class capacity. It only applies to 'Attended' status entries, while 'Missed' status entries are not counted towards the capacity limit.</p>
                        </div>

                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $message_type; ?>">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="class_id" class="form-label">Select Class</label>
                                <select name="class_id" id="class_id" class="form-select" required>
                                    <option value="">Select a class...</option>
                                    <?php while ($class = $classes_result->fetch_assoc()): ?>
                                        <option value="<?php echo $class['Class_ID']; ?>">
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
                                        <option value="<?php echo $member['Member_ID']; ?>">
                                            <?php echo htmlspecialchars($member['Name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" 
                                       value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="Attended">Attended</option>
                                    <option value="Missed">Missed</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Test Attendance</button>
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