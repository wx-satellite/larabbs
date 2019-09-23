<?php



// 将当前路由的"."换成"-"作为前端的类名
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

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

// 根据指定的query值返回"active"
if(!function_exists("is_query")) {
    function is_query($query, $value) {
        return request()->input($query)==$value?"active":"";
    }
}

if(!function_exists("make_excerpt")){
    function make_excerpt($excerpt, $length=200) {
        // strip_tags()去掉字符串中的html标签
        $excerpt = trim(preg_replace('/\r\n|\r|\n+/'," ", strip_tags($excerpt)));
        return Str::limit($excerpt, $length);
    }
}



