<?php

require 'auth.php';
require 'db.php';

$stmt = $pdo->prepare(
    'SELECT
        posts.id,
        posts.user_id,
        posts.content,
        posts.is_edited,
        posts.created_at,
        posts.updated_at,
        users.name
     FROM posts
     INNER JOIN users
     ON posts.user_id = users.id
     ORDER BY posts.created_at DESC'
);

$stmt->execute();
$posts = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Site</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <div class="topbar">
        <div>
            <h1>Blog Site</h1>
            <p>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></p>
        </div>

        <div>
            <a class="button" href="create_posts.php">Add Post</a>
            <a class="button danger" href="logout.php">Logout</a>
        </div>
    </div>

    <?php if (!$posts): ?>
        <div class="card">
            <p>No posts yet.</p>
        </div>
    <?php endif; ?>

    <?php foreach ($posts as $post): ?>

        <div class="card">

            <div class="post-header">
                <strong><?= htmlspecialchars($post['name']) ?></strong>
                <span><?= htmlspecialchars($post['created_at']) ?></span>
            </div>

            <p class="content">
                <?= nl2br(htmlspecialchars($post['content'])) ?>
            </p>

            <?php if ($post['is_edited']): ?>
                <span class="edited">(edited)</span>
            <?php endif; ?>

            <?php if ($post['user_id'] == $_SESSION['user_id']): ?>

                <div class="actions">

                    <a href="POSTS/edit_posts.php?id=<?= $post['id'] ?>">
                        Edit
                    </a>

                    <form
                        method="POST"
                        action="POSTS/delete_posts.php"
                        class="inline-form"
                    >
                        <input
                            type="hidden"
                            name="id"
                            value="<?= $post['id'] ?>"
                        >

                        <button
                            type="submit"
                            class="link-button danger-text"
                        >
                            Delete
                        </button>
                    </form>

                </div>

            <?php endif; ?>

            <div class="comments">

                <h3>Comments</h3>

                <?php

                $commentStmt = $pdo->prepare(
                    'SELECT
                        comments.id,
                        comments.post_id,
                        comments.user_id,
                        comments.content,
                        comments.is_edited,
                        comments.created_at,
                        users.name
                     FROM comments
                     INNER JOIN users
                     ON comments.user_id = users.id
                     WHERE comments.post_id = :post_id
                     ORDER BY comments.created_at ASC'
                );

                $commentStmt->execute([
                    ':post_id' => $post['id']
                ]);

                $comments = $commentStmt->fetchAll();

                ?>

                <?php if (!$comments): ?>
                    <p>No comments yet.</p>
                <?php endif; ?>

                <?php foreach ($comments as $comment): ?>

                    <div class="comment">

                        <strong>
                            <?= htmlspecialchars($comment['name']) ?>
                        </strong>

                        <p>
                            <?= nl2br(htmlspecialchars($comment['content'])) ?>
                        </p>

                        <?php if ($comment['is_edited']): ?>
                            <span class="edited">(edited)</span>
                        <?php endif; ?>

                        <?php if ($comment['user_id'] == $_SESSION['user_id']): ?>

                            <div class="actions">

                                <a href="COMMENTS/edit_comments.php?id=<?= $comment['id'] ?>">
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="delete_comments.php"
                                    class="inline-form"
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= $comment['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="link-button danger-text"
                                    >
                                        Delete
                                    </button>

                                </form>

                            </div>

                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>

                <form
                    method="POST"
                    action="create_comments.php"
                >

                    <input
                        type="hidden"
                        name="post_id"
                        value="<?= $post['id'] ?>"
                    >

                    <textarea
                        name="content"
                        placeholder="Write a comment..."
                        required
                        maxlength="1000"
                    ></textarea>

                    <button type="submit">
                        Comment
                    </button>

                </form>

            </div>

        </div>

    <?php endforeach; ?>

</div>

</body>
</html>