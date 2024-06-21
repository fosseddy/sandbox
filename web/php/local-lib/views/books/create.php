<?php require_once view\partial("layout/head") ?>

<h1><?= $title ?></h1>

<form class="form-group" method="POST">
    <label>
        Title:
        <input value="<?= $book->title ?? "" ?>" name="title">
    </label>
    <label>
        Author:
        <select name="author-id">
            <?php foreach ($authors as $a): ?>
                <?php if (isset($book) && $book->author_id == $a->id): ?>
                    <option value="<?= $a->id ?>" selected><?= $a->name() ?></option>
                <?php else: ?>
                    <option value="<?= $a->id ?>"><?= $a->name() ?></option>
                <?php endif ?>
            <?php endforeach ?>
        </select>
    </label>
    <label>
        Summary:
        <input value="<?= $book->summary ?? "" ?>" name="summary">
    </label>
    <label>
        ISBN:
        <input value="<?= $book->ISBN ?? "" ?>" name="ISBN">
    </label>
    <label>
        Genre:
        <?php foreach ($genres as $g): ?>
            <label>
                <?= $g->name ?>
                <?php if (isset($book) && in_array($g->id, $book->genres)): ?>
                    <input value="<?= $g->id ?>" name="genre[]" checked type="checkbox">
                <?php else: ?>
                    <input value="<?= $g->id ?>" name="genre[]" type="checkbox">
                <?php endif ?>
            </label>
        <?php endforeach ?>
    </label>
    <button class="btn btn-primary" type="submit">Submit</button>
</form>

<?php if (isset($errors)): ?>
    <ul>
        <?php foreach ($errors as $e): ?>
            <li class="text-danger"><?= $e ?></li>
        <?php endforeach ?>
    </ul>
<?php endif ?>

<?php require_once view\partial("layout/footer") ?>
