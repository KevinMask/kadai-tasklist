<?php

// デフォルトのコメント部分は省略

Route::get('/', 'TasksController@index');

Route::resource('tasks', 'TasksController');

// ユーザ登録
Route::get('signup', 'Auth\RegisterController@showRegistrationForm')->name('signup.get');
Route::post('signup', 'Auth\RegisterController@register')->name('signup.post');

// 認証
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login')->name('login.post');
Route::get('logout', 'Auth\LoginController@logout')->name('logout.get');

//認証付きのルーティング
Route::group(['middleware' => ['auth']], function () {
Route::resource('tasks', 'TasksController', ['only' => ['create', 'store', 'destroy', 'update', 'show', 'index']]);
});
