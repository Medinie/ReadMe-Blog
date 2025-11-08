<?php
require_once 'config.php';
requireLogin();

$blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch blog to get image filename
$stmt = $conn->prepare("SELECT image FROM blogpost WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $blog_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $blog = $result->fetch_assoc();
    
    // Delete the blog post
    $delete_stmt = $conn->prepare("DELETE FROM blogpost WHERE id = ? AND user_id = ?");
    $delete_stmt->bind_param("ii", $blog_id, $_SESSION['user_id']);
    
    if ($delete_stmt->execute()) {
        // Delete associated image if exists
        if ($blog['image']) {
            deleteImage($blog['image']);
        }
        header('Location: index.php');
    } else {
        header('Location: view.php?id=' . $blog_id);
    }
} else {
    header('Location: index.php');
}

exit();
?>