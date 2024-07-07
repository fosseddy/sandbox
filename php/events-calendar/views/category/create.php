<?php render_view("header", ["title" => "Create category"]) ?>

<?php render_view("category/form", [
    "name" => $name,
    "errors" => $errors,
    "button" => "Create"
]) ?>

<?php render_view("footer") ?>
