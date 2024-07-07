<?php render_view("header", ["title" => "Dashboard"]) ?>

<ul>
    <li><a href="/category">Category</a></li>
    <li><a href="/event">Events</a></li>
    <li><a href="/auth/logout.php">Logout</a></li>
</ul>

<?php render_view("footer") ?>
