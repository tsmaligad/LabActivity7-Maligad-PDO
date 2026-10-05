<?php

require 'auth.php';
require 'db.php';

$id = $_GET['id'] ?? null;

if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT * FROM posts
     WHERE id = :id
     AND user_id = :user_id'
);

$stmt->execute([
    ':id' => $id,
    ':user_id' => $_SESSION['user_id']
]);

$post = $stmt->fetch();

if (!$post) {
    header('Location: index.php');
    exit;
}

$content = $post['content'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content'] ?? '');

    if ($content === '') {
        $error = 'Post content is required.';
    } elseif (strlen($content) > 5000) {
        $error = 'Post is too long.';
    } else {
        $stmt = $pdo->prepare(
            'UPDATE posts
             SET content = :content,
                 is_edited = 1
             WHERE id = :id
             AND user_id = :user_id'
        );

        $stmt->execute([
            ':content' => $content,
            ':id' => $id,
            ':user_id' => $_SESSION['user_id']
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
    <title>Edit Post</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container form-container">
    <h1>Edit Post</h1>

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

        <button type="submit">Save Changes</button>
        <a href="index.php">Cancel</a>
    </form>
</div>

</body>
</html>