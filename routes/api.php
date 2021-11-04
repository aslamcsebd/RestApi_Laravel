<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Get api for show user
Route::get('/users/{id?}/', [UserController::class, 'showUser']);

//Post api for add user
Route::post('/add-user/', [UserController::class, 'addUser']);

//Post api for add multiple user
Route::post('/add-multiple-user/', [UserController::class, 'addMultipleUser']);

//Put api for update user details
Route::put('/update-user-details/{id}/', [UserController::class, 'updateUserDetails']);

//Patch api for update single record
Route::patch('/update-single-record/{id}/', [UserController::class, 'updateSingleRecord']);

//Delete api for delete single user
Route::delete('/delete-single-user/{id}/', [UserController::class, 'deleteSingleUser']);

//Delete api for delete multiple user
Route::delete('/delete-multiple-user/{ids}/', [UserController::class, 'deleteMultipleUser']);

//Delete api for delete single user with json
Route::delete('/delete-single-user-with-json/', [UserController::class, 'deleteSingleUserWithJson']);

//Delete api for delete multiple user with json
Route::delete('/delete-multiple-user-with-json/', [UserController::class, 'deleteMultipleUserWithJson']);

//Secure API with JWT
Route::delete('/user-delete-with-secure/{id}/', [UserController::class, 'userDeleteWithSecure']);

//Laravel passport : Passport is a library. Make a authentication by using laravel.
Route::post('/register-user-using-passport/', [UserController::class, 'registerUserUsingPassport']);

/*
   Install and configure laravel passport.

   1.Install Passport
   composer require laravel/passport

   If time out error comes then run the bellow command
   COMPOSER_MEMORY_LIMIT=-1 composer require laravel/passport

   2. Migration
   php artisan migrate

   3.Key Generate
   php artisan passport:install

   4.User Model
   use Laravel\Passport\HasApiTokens;
   use HasApiTokens, HasFactory, Notifiable;

   5. Update App\Providers\AuthServiceProvider
   use Laravel\Passport\Passport;

   In boot function add  
   Passport::routes();

   6. Update config/auth.php
      ->Authentication Guards->guards
      'api' => [
         'driver' => 'passport',
         'provider' => 'users',
      ],

   7. create route and function in controller
*/

//Laravel passport : login user using passport
Route::post('/login-user-using-passport/', [UserController::class, 'loginUserUsingPassport']);
