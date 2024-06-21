<?php require_once view\partial("layout/head") ?>

<h1><?= $title ?></h1>

<form method="POST">
    <div class="form-group">
        <label>
            Genre:
            <input value="<?= $genre->name ?? "" ?>" name="name" placeholder="Fantasy, Poetry, etc.">
        </label>
    </div>
    <button class="btn btn-primary" type="submit">Submit</button>
</form>

<?php if(isset($errors)): ?>
    <ul>
        <?php foreach($errors as $e): ?>
            <li class="text-danger"><?= $e ?></li>
        <?php endforeach ?>
    </ul>
<?php endif ?>

<?php require_once view\partial("layout/footer") ?>
