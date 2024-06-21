<?php view\render("view/partial-header", ["title" => "Update category"]) ?>

<?php view\render("category/partial-form", [
    "name" => $name,
    "errors" => $errors,
    "button" => "Update"
]) ?>

<?php view\render("view/partial-footer") ?>
