<?php require_once view\partial("layout/head") ?>

<h1><?= $title ?></h1>

<ul>
    <?php if (!$book_instances): ?>
        <li>There are no book copies in this library</li>
    <?php else: ?>
        <?php foreach ($book_instances as $b): ?>
            <li>
                <a href=<?= $b->url() ?>><?= "{$b->book->title} : $b->imprint" ?></a> -

                <?php if ($b->status === 0): ?>
                    <span class="text-success"><?= $b->status_string() ?></span>
                <?php elseif ($b->status === 1): ?>
                    <span class="text-danger"><?= $b->status_string() ?></span>
                <?php else: ?>
                    <span class="text-warning"><?= $b->status_string() ?></span>
                <?php endif ?>

                <?php if ($b->status !== 0): ?>
                    <span>(Due: <?= $b->due_back_formatted() ?>)</span>
                <?php endif ?>
            </li>
        <?php endforeach ?>
    <?php endif ?>
</ul>

<?php require_once view\partial("layout/footer") ?>
