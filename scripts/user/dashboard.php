<?php
require_once('../config/database.php');

$mysql_conn = get_mysql_connection();
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'members';

// Fetch data based on active tab
function fetchTableData($conn, $table) {
    $query = "";
    switch ($table) {
        case 'members':
            $query = "SELECT m.*, mp.Plan_Name, mp.Cost 
                     FROM Member m 
                     JOIN Membership_Plan mp ON m.Plan_ID = mp.Plan_ID 
                     ORDER BY m.Member_ID";
            break;
        case 'trainers':
            $query = "SELECT * FROM Trainer ORDER BY Trainer_ID";
            break;
        case 'classes':
            $query = "SELECT c.*, t.Name as Trainer_Name,
                     (SELECT COUNT(*) FROM Attendance a 
                      WHERE a.Class_ID = c.Class_ID AND a.Status = 'Attended' 
                      AND a.Date = CURDATE()) as Current_Attendance
                     FROM Class c
                     LEFT JOIN Trainer t ON c.Trainer_ID = t.Trainer_ID
                     ORDER BY c.Class_ID";
            break;
        case 'attendance':
            $query = "SELECT a.*, m.Name as Member_Name, c.Class_Name 
                     FROM Attendance a
                     JOIN Member m ON a.Member_ID = m.Member_ID
                     JOIN Class c ON a.Class_ID = c.Class_ID
                     ORDER BY a.Date DESC, a.Attendance_ID DESC
                     LIMIT 100";
            break;
        case 'payments':
            $query = "SELECT p.*, m.Name as Member_Name 
                     FROM Payment p
                     JOIN Member m ON p.Member_ID = m.Member_ID
                     ORDER BY p.Date DESC, p.Payment_ID DESC
                     LIMIT 100";
            break;
        case 'plans':
            $query = "SELECT mp.*, 
                     (SELECT COUNT(*) FROM Member m WHERE m.Plan_ID = mp.Plan_ID) as Member_Count
                     FROM Membership_Plan mp
                     ORDER BY mp.Plan_ID";
            break;
    }
    return $conn->query($query);
}

$result = fetchTableData($mysql_conn, $active_tab);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gym Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .dashboard-tab {
            cursor: pointer;
            padding: 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .dashboard-tab:hover {
            background-color: rgba(0,0,0,0.05);
        }
        .dashboard-tab.active {
            background-color: #0d6efd;
            color: white;
        }
        .table-responsive {
            max-height: 600px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2">
                <div class="list-group">
                    <a href="?tab=members" class="dashboard-tab <?php echo $active_tab == 'members' ? 'active' : ''; ?>">
                        <i class="bi bi-people-fill"></i> Members
                    </a>
                    <a href="?tab=trainers" class="dashboard-tab <?php echo $active_tab == 'trainers' ? 'active' : ''; ?>">
                        <i class="bi bi-person-badge"></i> Trainers
                    </a>
                    <a href="?tab=classes" class="dashboard-tab <?php echo $active_tab == 'classes' ? 'active' : ''; ?>">
                        <i class="bi bi-calendar-event"></i> Classes
                    </a>
                    <a href="?tab=attendance" class="dashboard-tab <?php echo $active_tab == 'attendance' ? 'active' : ''; ?>">
                        <i class="bi bi-check-circle"></i> Attendance
                    </a>
                    <a href="?tab=payments" class="dashboard-tab <?php echo $active_tab == 'payments' ? 'active' : ''; ?>">
                        <i class="bi bi-credit-card"></i> Payments
                    </a>
                    <a href="?tab=plans" class="dashboard-tab <?php echo $active_tab == 'plans' ? 'active' : ''; ?>">
                        <i class="bi bi-diagram-3"></i> Plans
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>
                            <?php
                            $titles = [
                                'members' => 'Members',
                                'trainers' => 'Trainers',
                                'classes' => 'Classes',
                                'attendance' => 'Attendance Records',
                                'payments' => 'Payment History',
                                'plans' => 'Membership Plans'
                            ];
                            echo $titles[$active_tab];
                            ?>
                        </h3>
                        <div>
                            <input type="text" class="form-control" id="tableSearch" placeholder="Search...">
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <?php
                                    if ($result && $result->num_rows > 0) {
                                        $fields = $result->fetch_fields();
                                        echo "<tr>";
                                        foreach ($fields as $field) {
                                            $header = str_replace('_', ' ', $field->name);
                                            echo "<th>" . ucwords($header) . "</th>";
                                        }
                                        echo "</tr>";
                                        $result->data_seek(0);
                                    }
                                    ?>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            foreach ($row as $key => $value) {
                                                if ($key == 'Cost' || $key == 'Amount' || $key == 'Salary') {
                                                    echo "<td>$" . number_format($value, 2) . "</td>";
                                                } elseif ($key == 'Schedule') {
                                                    echo "<td>" . date('H:i', strtotime($value)) . "</td>";
                                                } elseif ($key == 'Date') {
                                                    echo "<td>" . date('Y-m-d', strtotime($value)) . "</td>";
                                                } else {
                                                    echo "<td>" . htmlspecialchars($value) . "</td>";
                                                }
                                            }
                                            echo "</tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Table search functionality
        document.getElementById('tableSearch').addEventListener('keyup', function() {
            let searchText = this.value.toLowerCase();
            let table = document.querySelector('table');
            let rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                let row = rows[i];
                let cells = row.getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < cells.length; j++) {
                    let cell = cells[j];
                    if (cell.textContent.toLowerCase().indexOf(searchText) > -1) {
                        found = true;
                        break;
                    }
                }

                row.style.display = found ? '' : 'none';
            }
        });
    </script>
</body>
</html> 