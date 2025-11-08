<?php
require_once 'config.php';

$blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT b.*, u.username FROM blogpost b 
                        JOIN user u ON b.user_id = u.id 
                        WHERE b.id = ?");
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: index.php');
    exit();
}

$blog = $result->fetch_assoc();
$is_owner = isLoggedIn() && $_SESSION['user_id'] == $blog['user_id'];

// Function to format blog content with adjustable spacing
function formatBlogContent($content) {
    // Step 1: Remove any existing HTML tags for security
    $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
    
    // Step 2: Replace double line breaks with paragraph markers
    $content = preg_replace("/(\r\n|\n|\r){2,}/", "</p><p>", $content);
    
    // Step 3: Convert single line breaks to <br> tags
    $content = nl2br($content);
    
    // Step 4: Wrap in paragraph tags
    $content = '<p>' . $content . '</p>';
    
    return $content;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($blog['title']); ?> - ReadMe</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* paragraph spacing */
        .blog-content p {
            margin: 1.5em 0;
            line-height: 1.6;
        }
        
        .blog-content p:first-child {
            margin-top: 0;
        }
        
        .blog-content p:last-child {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-left">
                <span class="nav-label">ReadMe Blog</span>
            </div>
            <div class="nav-links">
                <a href="index.php" class="home-btn">Home</a>
                <?php if (isLoggedIn()): ?>
                    <a href="create.php">Create Blog</a>
                    <a href="logout.php">Logout</a>
                    <span class="welcome-text">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="blog-view">
            <?php if ($blog['image']): ?>
                <img src="uploads/<?php echo htmlspecialchars($blog['image']); ?>" 
                     alt="<?php echo htmlspecialchars($blog['title']); ?>" 
                     class="blog-view-image">
            <?php endif; ?>
            
            <h1><?php echo htmlspecialchars($blog['title']); ?></h1>
            
            <p class="meta">
                <span>By <?php echo htmlspecialchars($blog['username']); ?></span> | 
                <span><?php echo date('d/m/Y', strtotime($blog['created_at'])); ?></span>
                <?php if ($blog['created_at'] != $blog['updated_at']): ?>
                    | <span>Updated: <?php echo date('d/m/Y', strtotime($blog['updated_at'])); ?></span>
                <?php endif; ?>
            </p>
            
            <!--  Display content with 1.5em paragraph spacing -->
            <div class="blog-content">
                <?php echo formatBlogContent($blog['content']); ?>
            </div>
            
            <?php if ($is_owner): ?>
                <div class="blog-actions">
                    <a href="edit.php?id=<?php echo $blog['id']; ?>" class="btn-secondary">✏️ Edit</a>
                    <a href="delete.php?id=<?php echo $blog['id']; ?>" class="btn-danger">🗑️ Delete</a>
                </div>
            <?php endif; ?>
            
            <p style="margin-top: 30px;">
                <a href="index.php" class="btn-secondary">← Back to All Blogs</a>
            </p>
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