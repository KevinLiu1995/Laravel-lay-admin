<?php

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

Route::get('/', 'Admin\UserController@showLoginForm');
//一定是 Route::any, 因为微信服务端认证的时候是 GET, 接收用户消息时是 POST ！
Route::any('/wechat', 'WeChatController@serve');
