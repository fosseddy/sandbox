<?php
declare(strict_types = 1);

namespace category;
use PDO;

class Model
{
    public int $id;
    public string $name;
}

function validate(PDO $db, string $name, string $id = ""): array
{
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

        $s = $db->prepare($sql);
        $s->execute($params);

        if ($s->fetch())
        {
            $errors["name"] = "category with this name already exist";
        }
    }

    return $errors;
}
