<?php
require_once 'config.php';
requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    //  Proper handling of title and content
    $title = sanitize($_POST['title']);
    $content = $_POST['content']; // Keep line breaks as they are
    $user_id = $_SESSION['user_id'];
    
    if (empty($title) || empty($content)) {
        $error = 'Title and content are required';
    } else {
        // Handle image upload
        $image_filename = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_result = uploadImage($_FILES['image']);
            if ($upload_result === false) {
                $error = 'Invalid image file or size too large (max 5MB)';
            } else {
                $image_filename = $upload_result;
            }
        }
        
        if (empty($error)) {
            
            //  Single INSERT with prepared statement
            $stmt = $conn->prepare("INSERT INTO blogpost (user_id, title, content, image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $user_id, $title, $content, $image_filename);
            
            if ($stmt->execute()) {
                $blog_id = $conn->insert_id;
                header('Location: view.php?id=' . $blog_id);
                exit();
            } else {
                $error = 'Failed to create blog';
                if ($image_filename) {
                    deleteImage($image_filename);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blog - ReadMe</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-left">
                <span class="nav-label">ReadMe Blog </span>
            </div>
            <div class="nav-links">
                <a href="index.php" class="home-btn">Home</a>
                <a href="create.php">Create Blog</a>
                <a href="logout.php">Logout</a>
                <span class="welcome-text">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="form-box">
            <h1>✏️ Create New Blog</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>📝 Title</label>
                    <input type="text" name="title" required placeholder="Enter an engaging title...">
                </div>
                
                <div class="form-group">
                    <label>🖼️ Cover Image (Optional)</label>
                    <input type="file" name="image" accept="image/*">
                    <small style="color: #888;">Max size: 5MB. Formats: JPG, PNG, GIF</small>
                </div>
                
                <div class="form-group">
                    <label>📄 Content</label>
                    <textarea name="content" required placeholder="Share your story..."></textarea>
                </div>
                
                <button type="submit" class="btn-primary">🚀 Publish Blog</button>
                <a href="index.php" class="btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
    
    <footer class="footer">
        <div class="container">
            <div class="footer-left">Your Knowledge Partner</div>
            <div class="footer-right">Write, Learn, and Grow</div>
        </div>
    </footer>
    
    <script src="script.js"></script>
</body>
</html>