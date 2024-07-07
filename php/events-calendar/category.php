<?php
declare(strict_types = 1);

class Category
{
    public int $id;
    public string $name;
}

function validate_category(string $name, string $id = ""): array
{
    global $database;

    $errors = [];

    if (!$name)
    {
        $errors["name"] = "name is required";
    }
    else if (mb_strlen($name) > 100)
    {
        $errors["name"] = "name must have 100 or less characters";
    }
    else
    {
        $sql = "select id from category where name = ?";
        $params = [$name];

        if ($id)
        {
            $sql .= " and id <> ?";
            $params[] = $id;
        }

        $s = $database->prepare($sql);
        $s->execute($params);

        if ($s->fetch())
        {
            $errors["name"] = "category with this name already exist";
        }
    }

    return $errors;
}
