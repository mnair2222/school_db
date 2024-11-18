<?php
require_once 'config.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}


$stmt = $pdo->prepare("
    SELECT student.*, classes.name as class_name 
    FROM student 
    LEFT JOIN classes ON student.class_id = classes.class_id 
    WHERE student.id = ?
");
$stmt->execute([$_GET['id']]);
$student = $stmt->fetch();

if (!$student) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student - <?php echo htmlspecialchars($student['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Student Details</h2>
            <div>
                <a href="edit.php?id=<?php echo $student['id']; ?>" 
                   class="btn btn-warning">Edit</a>
                <a href="index.php" class="btn btn-secondary">Back to List</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <?php if ($student['image']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($student['image']); ?>" 
                                 alt="Student Image" 
                                 class="img-fluid rounded">
                        <?php else: ?>
                            <div class="alert alert-info">No image available</div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8">
                        <table class="table">
                            <tr>
                                <th width="150">Name:</th>
                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                            </tr>
                            <tr>
                                <th>Address:</th>
                                <td><?php echo nl2br(htmlspecialchars($student['address'])); ?></td>
                            </tr>
                            <tr>
                                <th>Class:</th>
                                <td><?php echo htmlspecialchars($student['class_name'] ?? 'Not Assigned'); ?></td>
                            </tr>
                            <tr>
                                <th>Created At:</th>
                                <td><?php echo date('Y-m-d H:i', strtotime($student['created_at'])); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>