<?php
require_once 'config.php';

// Fetch all blogs with author information
$query = "SELECT b.*, u.username FROM blogpost b 
          JOIN user u ON b.user_id = u.id 
          ORDER BY b.created_at DESC";
$result = $conn->query($query);


// Function to get blog preview with proper line break handling
// Shows at least 3 lines and removes ugly \r\n characters

function getBlogPreview($content, $maxLength = 200) {
    // Step 1: Convert line breaks to spaces for preview
    // This prevents \r\n from showing in the preview
    $content = str_replace(["\r\n", "\r", "\n"], ' ', $content);
    
    // Step 2: Remove HTML tags for security
    $content = strip_tags($content);
    
    // Step 3: Remove extra spaces (multiple spaces become one)
    $content = preg_replace('/\s+/', ' ', $content);
    
    // Step 4: Trim whitespace from beginning and end
    $content = trim($content);
    
    // Step 5: If content is shorter than max length, return as is
    if (strlen($content) <= $maxLength) {
        return htmlspecialchars($content);
    }
    
    // Step 6: Truncate at word boundary (don't cut words in half)
    $preview = substr($content, 0, $maxLength);
    $lastSpace = strrpos($preview, ' ');
    if ($lastSpace !== false) {
        $preview = substr($preview, 0, $lastSpace);
    }
    
    // Step 7: Return with "..." at the end
    return htmlspecialchars($preview) . '...';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReadMe - Share Your Stories</title>
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
        <h1 class="page-title">Latest Blogs</h1>
        
        <?php if ($result->num_rows > 0): ?>
            <div class="blog-grid">
                <?php while ($blog = $result->fetch_assoc()): ?>
                    <div class="blog-card">
                        <?php if ($blog['image']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($blog['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($blog['title']); ?>" 
                                 class="blog-card-image">
                        <?php endif; ?>
                        
                        <div class="blog-card-content">
                            <h2>
                                <a href="view.php?id=<?php echo $blog['id']; ?>">
                                    <?php echo htmlspecialchars($blog['title']); ?>
                                </a>
                            </h2>
                            <p class="meta">
                                <span>By <?php echo htmlspecialchars($blog['username']); ?></span>
                                <span><?php echo date('d/m/Y', strtotime($blog['created_at'])); ?></span>
                            </p>
                            <!-- Show clean preview without \r\n characters -->
                            <p><?php echo getBlogPreview($blog['content'], 200); ?></p>
                            <a href="view.php?id=<?php echo $blog['id']; ?>">Read More</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>No blogs available yet.</p>
                <?php if (isLoggedIn()): ?>
                    <a href="create.php" class="btn-primary">Create First Blog</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
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