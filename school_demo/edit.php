<?php
require_once 'config.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}


$stmt = $pdo->prepare("SELECT * FROM student WHERE id = ?");
$stmt->execute([$_GET['id']]);
$student = $stmt->fetch();

if (!$student) {
    header("Location: index.php");
    exit();
}


$stmt = $pdo->query("SELECT class_id, name FROM classes ORDER BY name");
$classes = $stmt->fetchAll();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $class_id = $_POST['class_id'] ?? null;
    
    if (empty($name)) {
        $error = "Name is required";
    } else {
        
        $image_path = $student['image']; 
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $file_name = $_FILES['image']['name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            if (in_array($file_ext, $allowed)) {
            
                $new_name = uniqid() . '.' . $file_ext;
                $upload_path = 'uploads/' . $new_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    
                    if ($student['image'] && file_exists('uploads/' . $student['image'])) {
                        unlink('uploads/' . $student['image']);
                    }
                    $image_path = $new_name;
                }
            }
        }
        
        try {
            $stmt = $pdo->prepare("
                UPDATE student 
                SET name = ?, email = ?, address = ?, class_id = ?, image = ? 
                WHERE id = ?
            ");
            
            $stmt->execute([$name, $email, $address, $class_id, $image_path, $_GET['id']]);
            header("Location: view.php?id=" . $_GET['id']);
            exit();
            
        } catch (PDOException $e) {
            $error = "Error updating student: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Student</h2>
            <div>
                <a href="view.php?id=<?php echo $student['id']; ?>" class="btn btn-info">View</a>
                <a href="index.php" class="btn btn-secondary">Back to List</a>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo htmlspecialchars($student['name']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($student['email']); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" 
                                  rows="3"><?php echo htmlspecialchars($student['address']); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="class_id" class="form-label">Class</label>
                        <select class="form-control" id="class_id" name="class_id">
                            <option value="">Select Class</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo $class['class_id']; ?>" 
                                        <?php echo ($student['class_id'] == $class['class_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($class['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Image (JPG, PNG only)</label>
                        <?php if ($student['image']): ?>
                            <div class="mb-2">
                                <img src="uploads/<?php echo htmlspecialchars($student['image']); ?>" 
                                     alt="Current Image" style="max-width: 200px;">
                                <p class="text-muted">Current image</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="image" name="image" accept=".jpg,.jpeg,.png">
                        <small class="text-muted">Leave empty to keep current image</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Student</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>