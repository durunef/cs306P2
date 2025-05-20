<?php
require_once '../config/database.php';

$mysql_conn = get_mysql_connection();
$mongo_db = get_mongo_db();

// Get all members for dropdown
$members_query = "SELECT member_id, name FROM members ORDER BY name";
$members_result = $mysql_conn->query($members_query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = (int)$_POST['member_id'];
    $subject = $_POST['subject'];
    $description = $_POST['description'];
    
    $tickets_collection = $mongo_db->tickets;
    
    $ticket = [
        'member_id' => $member_id,
        'subject' => $subject,
        'description' => $description,
        'status' => 'open',
        'created_at' => date('Y-m-d H:i:s'),
        'comments' => []
    ];
    
    $result = $tickets_collection->insertOne($ticket);
    
    if ($result->getInsertedCount() > 0) {
        header('Location: support_confirm.php?id=' . $result->getInsertedId());
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Support Ticket - Fitness Center</title>
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
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Create New Support Ticket</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="member_id" class="form-label">Select Member</label>
                                <select name="member_id" id="member_id" class="form-select" required>
                                    <option value="">Select a member...</option>
                                    <?php while ($member = $members_result->fetch_assoc()): ?>
                                        <option value="<?php echo $member['member_id']; ?>">
                                            <?php echo htmlspecialchars($member['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Create Ticket</button>
                                <a href="support.php" class="btn btn-secondary">Cancel</a>
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