<?php render_view("header", ["title" => "Update category"]) ?>

<?php render_view("category/form", [
    "name" => $name,
    "errors" => $errors,
    "button" => "Update"
]) ?>

<?php render_view("footer") ?>
