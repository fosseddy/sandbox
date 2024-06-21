<?php require_once view\partial("layout/head") ?>

<h1><?= $title ?></h1>

<p>Welcome to <em>LocalLibrary</em>, a very basic website developed to practice PHP.</p>

<h2>Dynamic content</h2>

<p>The library has the following record counts:</p>

<ul>
    <li><strong>Books:</strong> <?= $book["count"] ?></li>
    <li><strong>Copies:</strong> <?= $book_instance["count"] ?></li>
    <li><strong>Copies available:</strong> <?= $book_instance_available["count"] ?></li>
    <li><strong>Authors:</strong> <?= $author["count"] ?></li>
    <li><strong>Genres:</strong> <?= $genre["count"] ?></li>
</ul>

<?php require_once view\partial("layout/footer") ?>
