<?php

use Illuminate\Support\Facades\Route;

Route::get("/{slug?}", function () {
    if (!request()->ajax()) {
        return view("app");
    }
})->where("slug", "[\/\w\.-]*");
