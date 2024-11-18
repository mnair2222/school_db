<?php
require_once 'config.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

try {
    
    $stmt = $pdo->prepare("SELECT image FROM student WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $student = $stmt->fetch();
    
    if ($student) {
        
        if ($student['image'] && file_exists('uploads/' . $student['image'])) {
            unlink('uploads/' . $student['image']);
        }
        
        
        $stmt = $pdo->prepare("DELETE FROM student WHERE id = ?");
        $stmt->execute([$_GET['id']]);
    }
    
    header("Location: index.php");
    exit();
    
} catch (PDOException $e) {
    die("Error deleting student: " . $e->getMessage());
}
?>