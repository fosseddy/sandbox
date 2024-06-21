<?php view\render("view/partial-header", ["title" => "Create category"]) ?>

<?php view\render("category/partial-form", [
    "name" => $name,
    "errors" => $errors,
    "button" => "Create"
]) ?>

<?php view\render("view/partial-footer") ?>
