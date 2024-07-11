<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController
{
    function getEvents(Request $req): string
    {
        return "<div><a href='/admin/logout'>logout</a></div>";
    }
}
