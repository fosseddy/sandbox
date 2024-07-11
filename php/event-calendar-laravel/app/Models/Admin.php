<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Admin extends Model implements AuthenticatableContract
{
    use Authenticatable, HasFactory;

    public $timestamps = false;

    protected $hidden = ["password"];

    protected function casts(): array
    {
        return ["password" => "hashed"];
    }
}
