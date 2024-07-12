<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Models\Category;

class EventController
{
    public $formFields = [
        "name",
        "date",
        "category_id",
        "duration",
        "location",
        "organizer",
    ];

    function getIndex(Request $req): View
	{
        $events = Event::orderBy("created_at", "desc")->get();
        return view("event.index", ["events" => $events]);
	}

    function getCreate(Request $req): View
	{
        $form = [];
        foreach ($this->formFields as $f)
        {
            $form[$f] = $req->old($f, "");
        }

        return view("event.create", [
            "form" => $form,
            "cats" => Category::all()
        ]);
	}

    function postCreate(EventRequest $req): RedirectResponse
	{
        $e = new Event();

        $e->name = $req->name;
        $e->date = $req->date;
        $e->category_id = $req->category_id;
        $e->duration = $req->duration ?? 0;
		$e->location = $req->location ?? "";
        $e->organizer = $req->organizer ?? "";

        if ($req->hasFile("file"))
        {
            $e->image = $req->file->hashName();
            $req->file->store("uploads", "public");
        }

        $e->save();

        return redirect("/event");
	}

    function getUpdate(Request $req, Event $e): View
	{
        $form = [];
        foreach ($this->formFields as $f)
        {
            $form[$f] = $req->old($f, $e->$f);
        }

        return view("event.update", [
            "event" => $e,
            "form" => $form,
            "cats" => Category::all()
        ]);
	}

    function postUpdate(EventRequest $req, Event $e): RedirectResponse
	{
        $e->name = $req->name;
        $e->date = $req->date;
        $e->category_id = $req->category_id;
        $e->duration = $req->duration ?? 0;
		$e->location = $req->location ?? "";
        $e->organizer = $req->organizer ?? "";

        if ($req->hasFile("file"))
        {
            $e->image = $req->file->hashName();
            $req->file->store("uploads", "public");
        }

        $e->save();

        return redirect("/event");
	}

    function postDelete(Event $e): RedirectResponse
	{
        $e->delete();

        if ($e->image)
        {
            Storage::disk("public")->delete("uploads/$e->image");
        }

        return redirect("/event");
	}

    function postDeleteImage(Event $e): RedirectResponse
	{
        if ($e->image)
        {
            Storage::disk("public")->delete("uploads/$e->image");
            $e->image = "";
            $e->save();
        }

        return redirect("/event/update/$e->id");
	}
}
