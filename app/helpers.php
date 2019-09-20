<?php



// 将当前路由的"."换成"-"作为前端的类名
use Illuminate\Support\Facades\Route;

if(!function_exists("route_class")) {
    function route_class() {
        return str_replace(".",'-', Route::currentRouteName());
    }
}



// 当当前页面是指定分类时，导航高亮
if(!function_exists("is_active")) {
    function is_active($id) {
        $url = \Illuminate\Support\Facades\URL::current();
        $urlInfo = parse_url($url);
        $path = $urlInfo["path"]??"";
        return "/categories/{$id}" == $path ? "active": "";
    }
}



