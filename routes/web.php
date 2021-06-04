<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('messages', 'ChatController@messages');
Route::prefix('chat')->group(function () {
    Route::get('/', 'ChatController@index');
    Route::post('/', 'ChatController@store');
    Route::get('/create-group', 'ChatController@createGroup')->name('chatGroup.create');
    Route::get('/groups', 'ChatController@index')->name('chatGroup.index');
    Route::post('/create-group', 'ChatController@storeGroup')->name('chatGroup.store');
    Route::get('/user-related-users-and-groups', 'ChatController@userRelatedUsersAndGroups');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
