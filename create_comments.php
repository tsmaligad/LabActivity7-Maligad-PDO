<?php

require 'auth.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$postId = $_POST['post_id'] ?? null;
$content = trim($_POST['content'] ?? '');

if (
    !$postId ||
    !filter_var($postId, FILTER_VALIDATE_INT) ||
    $content === '' ||
    strlen($content) > 1000
) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT id FROM posts WHERE id = :id'
);

$stmt->execute([
    ':id' => $postId
]);

if (!$stmt->fetch()) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare(
    'INSERT INTO comments (post_id, user_id, content)
     VALUES (:post_id, :user_id, :content)'
);

$stmt->execute([
    ':post_id' => $postId,
    ':user_id' => $_SESSION['user_id'],
    ':content' => $content
]);

header('Location: index.php');
exit;