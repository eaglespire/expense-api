<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('greet-users', function (){
    return response()->json([
        'status' => 'success',
        'message' => 'Greet users'
    ]);
});
