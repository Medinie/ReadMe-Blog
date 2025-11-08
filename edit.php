<?php
require_once 'config.php';
requireLogin();

$blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';

// Fetch blog and check ownership
$stmt = $conn->prepare("SELECT * FROM blogpost WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $blog_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: index.php');
    exit();
}

$blog = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $content = $_POST['content']; // Keep line breaks as they are
    
    if (empty($title) || empty($content)) {
        $error = 'Title and content are required';
    } else {
        // Handle image upload
        $image_filename = $blog['image']; // Keep existing image by default
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_result = uploadImage($_FILES['image']);
            if ($upload_result === false) {
                $error = 'Invalid image file or size too large (max 5MB)';
            } else {
                // Delete old image if exists
                if ($blog['image']) {
                    deleteImage($blog['image']);
                }
                $image_filename = $upload_result;
            }
        }
        
        if (empty($error)) {
            $stmt = $conn->prepare("UPDATE blogpost SET title = ?, content = ?, image = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("sssii", $title, $content, $image_filename, $blog_id, $_SESSION['user_id']);
            
            if ($stmt->execute()) {
                header('Location: view.php?id=' . $blog_id);
                exit();
            } else {
                $error = 'Failed to update blog';
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
    <title>Edit Blog - ReadMe</title>
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
            <h1>✏️ Edit Blog</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>📝 Title</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($blog['title']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>🖼️ Cover Image</label>
                    <?php if ($blog['image']): ?>
                        <div class="image-preview">
                            <p>Current Image:</p>
                            <img src="uploads/<?php echo htmlspecialchars($blog['image']); ?>" 
                                 alt="Current" style="max-width: 300px; border-radius: 10px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/*">
                    <small style="color: #888;">Leave empty to keep current image. Max size: 5MB</small>
                </div>
                
                <div class="form-group">
                    <label>📄 Content</label>
                    <textarea name="content" required><?php echo htmlspecialchars($blog['content']); ?></textarea>
                </div>
                
                <button type="submit" class="btn-primary">💾 Update Blog</button>
                <a href="view.php?id=<?php echo $blog_id; ?>" class="btn-secondary">Cancel</a>
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