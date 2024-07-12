<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EventController;

Route::redirect("/", "/event");

Route::controller(AuthController::class)->group(function() {
    Route::get("/login", "getLogin")->name("login");
    Route::post("/login", "postLogin");
    Route::get("/logout", "getLogout");
});

Route::controller(CategoryController::class)->group(function() {
    Route::middleware("auth")->group(function() {
        Route::prefix("category")->group(function() {
            Route::get("/", "getIndex");

            Route::get("/create", "getCreate");
            Route::post("/create", "postCreate");

            Route::get("/update/{cat}", "getUpdate");
            Route::post("/update/{cat}", "postUpdate");

            Route::post("/delete/{cat}", "postDelete");
        });
    });
});

Route::controller(EventController::class)->group(function() {
    Route::middleware("auth")->group(function() {
        Route::prefix("event")->group(function() {
            Route::get("/", "getIndex");

            Route::get("/create", "getCreate");
            Route::post("/create", "postCreate");

            Route::get("/update/{e}", "getUpdate");
            Route::post("/update/{e}", "postUpdate");

            Route::post("/delete/{e}", "postDelete");
            Route::post("/delete-image/{e}", "postDeleteImage");
        });
    });
});

