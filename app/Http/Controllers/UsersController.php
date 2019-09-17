<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{

    //当请求 http://larabbs.test/users/1 Laravel 将会自动查找 ID 为 1 的用户并赋值到变量 $user 中，
    //如果数据库中找不到对应的模型实例，会自动生成 HTTP 404 响应，
    public function show(User $user) {
        return view("users.show",compact("user"));
    }
}
