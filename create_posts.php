<?php

require 'auth.php';
require 'db.php';

$content = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content'] ?? '');

    if ($content === '') {
        $error = 'Post content is required.';
    } elseif (strlen($content) > 5000) {
        $error = 'Post is too long.';
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO posts (user_id, content)
             VALUES (:user_id, :content)'
        );

        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':content' => $content
        ]);

        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Post</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container form-container">
    <h1>Add Post</h1>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Content</label>

        <textarea
            name="content"
            required
            maxlength="5000"
        ><?= htmlspecialchars($content) ?></textarea>

        <button type="submit">Post</button>
        <a href="index.php">Cancel</a>
    </form>
</div>

</body>
</html>