<form method="POST">
    <div>
        <label>
            Username <br>
            <input name="name" value="<?= $name ?? "" ?>">
        </label>
    </div>

    <div>
        <label>
            Password <br>
            <input name="password" type="password">
        </label>
    </div>

    <button type="submit">Submit</button>
</form>

<?php if ($errors ?? false): ?>
    <ul>
        <?php foreach ($errors as $e): ?>
            <li><?= $e ?></li>
        <?php endforeach ?>
    </ul>
<?php endif ?>
