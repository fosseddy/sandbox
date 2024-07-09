<?php render_view("header", ["title" => "Category"]) ?>

<?php if ($categories): ?>
    <?php foreach ($categories as $cat): ?>
        <div style="width: 50%; display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
            <p><?= $cat->name ?></p>
            <div>
                <a href="/category/update.php?id=<?= $cat->id ?>">Update</a>
                <a href="/category/delete.php?id=<?= $cat->id ?>">Delete</a>
            </div>
        </div>
    <?php endforeach ?>
    <script>
        for (const a of document.querySelectorAll("a[href*='/category/delete']")) {
            a.addEventListener("click", (e) => {
                e.preventDefault();
                if (window.confirm("Are you sure you want to delete this category?")) {
                    window.location = e.target.href;
                }
            });
        }
    </script>
<?php else: ?>
    <p>There are no categories. <a href="/category/create.php">Create</a></p>
<?php endif ?>

<?php render_view("footer") ?>
