<?php
require_once 'config.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'create' && !empty($_POST['class_name'])) {
        $stmt = $pdo->prepare("INSERT INTO classes (name) VALUES (?)");
        $stmt->execute([$_POST['class_name']]);
        header("Location: classes.php");
        exit();
    }
    
    
    if ($_POST['action'] == 'delete' && !empty($_POST['class_id'])) {
        try {
            
            $check = $pdo->prepare("SELECT COUNT(*) FROM student WHERE class_id = ?");
            $check->execute([$_POST['class_id']]);
            if ($check->fetchColumn() > 0) {
                $error = "Cannot delete class that has students assigned to it.";
            } else {
                $stmt = $pdo->prepare("DELETE FROM classes WHERE class_id = ?");
                $stmt->execute([$_POST['class_id']]);
                header("Location: classes.php");
                exit();
            }
        } catch(PDOException $e) {
            $error = "Error deleting class: " . $e->getMessage();
        }
    }
}


$stmt = $pdo->query("SELECT * FROM classes ORDER BY name");
$classes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Classes</h2>
            <a href="index.php" class="btn btn-secondary">Back to Students</a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Add New Class Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Add New Class</h5>
            </div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <input type="hidden" name="action" value="create">
                    <div class="col-auto">
                        <input type="text" class="form-control" name="class_name" 
                               placeholder="Enter class name" required>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Add Class</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Classes List -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Existing Classes</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Class Name</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($classes as $class): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($class['name']); ?></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($class['created_at'])); ?></td>
                                <td>
                                    <form method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this class?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="class_id" 
                                               value="<?php echo $class['class_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>