<?php render_view("header", ["title" => "Events"]) ?>

<?php if ($events): ?>
    <?php foreach ($events as $e): ?>
        <div style="width: 50%; display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
            <p><?= $e->name ?></p>
            <div>
                <a href="/event/update.php?id=<?= $e->id ?>">Update</a>
                <a href="/event/delete.php?id=<?= $e->id ?>">Delete</a>
            </div>
        </div>
    <?php endforeach ?>
<?php else: ?>
    <p>There are no events. <a href="/event/create.php">Create</a></p>
<?php endif ?>

<?php render_view("footer") ?>
