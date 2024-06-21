<?php view\render("view/partial-header", ["title" => "Login"]) ?>

<form method="POST">
    <div class="form-field">
        <input name="name" placeholder="name" value="<?= $name ?>">
        <small><?= $errors["name"] ?? "&nbsp;" ?></small>
    </div>
    <div class="form-field">
        <input type="password" name="password" placeholder="password" value="<?= $pass ?>">
        <small><?= $errors["password"] ?? "&nbsp;" ?></small>
    </div>
    <button type="submit">Login</button>
</form>

<?php view\render("view/partial-footer") ?>
