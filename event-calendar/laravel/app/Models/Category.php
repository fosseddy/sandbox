<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Event;

class Category extends Model
{
    use HasFactory;

    function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
