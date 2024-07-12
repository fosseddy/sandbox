<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;

class CategoryController
{
    function getIndex(): View
    {
        $cats = Category::orderBy("created_at", "desc")->get();
        return view("category.index", ["categories" => $cats]);
    }

    function getCreate(Request $req): View
    {
        $form = ["name" => $req->old("name", "")];
        return view("category.create", ["form" => $form]);
    }

    function postCreate(CategoryRequest $req): RedirectResponse
    {
        $cat = new Category();
        $cat->name = $req->name;
        $cat->save();

        return redirect("/category");
    }

    function getUpdate(Request $req, Category $cat): View
    {
        $form = ["name" => $req->old("name", $cat->name)];
        return view("category.update", ["form" => $form]);
    }

    function postUpdate(CategoryRequest $req, Category $cat): RedirectResponse
    {
        $cat->name = $req->name;
        $cat->save();

        return redirect("/category");
    }

    function postDelete(Category $cat): RedirectResponse
    {
        $images = $cat->events->where("image", "!=", "")->pluck("image");
        $cat->delete();

        foreach ($images as $img)
        {
            Storage::disk("public")->delete("uploads/$img");
        }

        return redirect("/category");
    }
}
