<?php require_once view\partial("layout/head") ?>

<h1>Title: <?= $book->title ?></h1>

<p><strong>Author:</strong> <a href="<?= $book->author->url() ?>"><?= $book->author->name() ?></a></p>
<p><strong>Summary:</strong> <?= $book->summary ?></p>
<p><strong>ISBN:</strong> <?= $book->ISBN ?></p>
<p><strong>Genre:</strong>
    <?php foreach ($genres as $i => $g): ?>
        <a href="<?= $g->url() ?>"><?= $g->name ?></a><?= $i < count($genres) - 1 ? "," : "" ?>
    <?php endforeach ?>
</p>

<div>
    <h2>Copies</h2>
    <?php if (!$book_instances): ?>
        <p>There are no copies of this book in the library</p>
    <?php else: ?>
        <?php foreach ($book_instances as $b): ?>
            <hr>

            <?php if ($b->status === 0): ?>
                <p class="text-success"><?= $b->status_string() ?></p>
            <?php elseif ($b->status === 1): ?>
                <p class="text-danger"><?= $b->status_string() ?></p>
            <?php else: ?>
                <p class="text-warning"><?= $b->status_string() ?></p>
            <?php endif ?>

            <p><strong>Imprint:</strong> <?= $b->imprint ?></p>

            <?php if ($b->status !== 0): ?>
                <p><strong>Due back:</strong> <?= $b->due_back_formatted() ?></p>
            <?php endif ?>

            <p><strong>Id:</strong> <a href="<?= $b->url() ?>"><?= $b->id ?></a></p>
        <?php endforeach ?>
    <?php endif ?>
</div>

<?php require_once view\partial("layout/footer") ?>
