<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;

class CategoryController
{
    function getIndex(): View
    {
        $cats = Category::orderBy("created_at", "desc")->get();
        return view("category.index", ["categories" => $cats]);
    }

    function getCreate(): View
    {
        return view("category.create");
    }

    function postCreate(CategoryRequest $req): RedirectResponse
    {
        $req->validated();

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
        $req->validated();

        $cat->name = $req->name;
        $cat->save();

        return redirect("/category");
    }

    function getDelete(Category $cat): RedirectResponse
    {
        // TODO(art): delete events and their images
        $cat->delete();
        return redirect("/category");
    }
}
