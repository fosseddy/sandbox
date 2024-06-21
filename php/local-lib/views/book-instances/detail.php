<?php require_once view\partial("layout/head") ?>

<h1>ID: <?= $book_instance->id ?></h1>
<p><strong>Title:</strong> <a href="<?= $book_instance->book->url() ?>"><?= $book_instance->book->title ?></a></p>
<p><strong>Imprint:</strong> <?= $book_instance->imprint ?></p>
<p><strong>Status:</strong>
    <?php if ($book_instance->status === 0): ?>
        <span class="text-success"><?= $book_instance->status_string() ?></span>
    <?php elseif ($book_instance->status === 1): ?>
        <span class="text-danger"><?= $book_instance->status_string() ?></span>
    <?php else: ?>
        <span class="text-warning"><?= $book_instance->status_string() ?></span>
    <?php endif ?>
</p>

<?php if ($book_instance->status !== 0): ?>
    <p><strong>Due back:</strong> <?= $book_instance->due_back_formatted() ?></p>
<?php endif ?>

<?php require_once view\partial("layout/footer") ?>
