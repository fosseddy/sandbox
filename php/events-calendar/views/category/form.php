<form method="POST">
    <div class="form-field">
        <input name="name" placeholder="name" value="<?= $name ?>">
        <small><?= $errors["name"] ?? "&nbsp;" ?></small>
    </div>
    <button type="submit"><?= $button ?></button>
</form>
