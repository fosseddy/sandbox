<?php require_once view\partial("layout/head") ?>

<h1><?= $title ?></h1>

<form method="POST">
    <div class="form-group">
        <label>
            First Name:
            <input value="<?= $author->first_name ?? "" ?>" name="first-name">
        </label>
        <label>
            Family Name:
            <input value="<?= $author->family_name ?? "" ?>" name="family-name">
        </label>
        <label>
            Date of birth:
            <input value="<?= $author->date_of_birth ?? "" ?>" name="date-of-birth" type="date">
        </label>
        <label>
            Date of death:
            <input value="<?= $author->date_of_death ?? "" ?>" name="date-of-death" type="date">
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
