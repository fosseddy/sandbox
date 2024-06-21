<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShURL</title>
</head>
<body>
    <h1>ShURL</h1>

    <form method="POST">
        <label>
            URL <br>
            <input name="long-url" value="<?= $long_url ?? "" ?>" style="width: 20rem;">
        </label>
        <button type="submit">Submit</button>
    </form>

    <?php if ($errors ?? false): ?>
        <ul>
            <?php foreach ($errors as $e): ?>
                <li><?= $e ?></li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>

    <?php if ($url ?? false): ?>
        <br>
               <!-- this must be in config or something -->
        <input value="<?= "{$_SERVER['HTTP_HOST']}/?id=$url->short_url" ?>" style="width: 30rem;">
    <?php endif ?>
</body>
</html>
