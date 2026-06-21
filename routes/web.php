<?php

use Src\Route;
Route::add(["GET"], "/", [Controller\Public\Post::class, "index"]);
