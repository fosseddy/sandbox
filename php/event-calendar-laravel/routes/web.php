<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
//use App\Http\Controllers\EventController;

Route::redirect("/", "/category");

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

            Route::get("/delete/{cat}", "getDelete");
        });
    });
});

//Route::controller(EventController::class)->group(function() {
//    Route::middleware("auth")->group(function() {
//        Route::get("/events", "getEvents");
//        Route::prefix("event")->group(function() {
//            Route::post("/", "postEvent");
//            Route::get("/{event}", "getEvent");
//            Route::put("/{event}", "putEvent");
//            Route::delete("/{event}", "deleteEvent");
//            Route::get("/nearest/{num}", "getEventNearest");
//        });
//    });
//});

