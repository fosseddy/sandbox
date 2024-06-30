<?php
declare(strict_types = 1);

namespace event;
use category;
use PDO;

class Model
{
    public int $id;
    public string $name;
	public string $date;
	public int $category_id;

	public string $duration;
	public string $location;
	public string $organizer;
	public string $image;

    public category\Model $category;
}
