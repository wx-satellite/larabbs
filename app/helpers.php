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


// Str类中存在很多常用的字符串处理函数：limit()，random()等等
if(!function_exists("make_excerpt")){
    function make_excerpt($excerpt, $length=200) {
        // strip_tags()去掉字符串中的html标签
        $excerpt = trim(preg_replace('/\r\n|\r|\n+/'," ", strip_tags($excerpt)));
        return Str::limit($excerpt, $length);
    }
}



function model_admin_link($title, $model)
{
    return model_link($title, $model, 'admin');
}

function model_link($title, $model, $prefix = '')
{
    // 获取数据模型的复数蛇形命名
    $model_name = model_plural_name($model);

    // 初始化前缀
    $prefix = $prefix ? "/$prefix/" : '/';

    // 使用站点 URL 拼接全量 URL
    $url = config('app.url') . $prefix . $model_name . '/' . $model->id;

    // 拼接 HTML A 标签，并返回
    return '<a href="' . $url . '" target="_blank">' . $title . '</a>';
}

function model_plural_name($model)
{
    // 从实体中获取完整类名，例如：App\Models\User
    $full_class_name = get_class($model);

    // 获取基础类名，例如：传参 `App\Models\User` 会得到 `User`
    $class_name = class_basename($full_class_name);

    // 蛇形命名，例如：传参 `User`  会得到 `user`, `FooBar` 会得到 `foo_bar`
    $snake_case_name = snake_case($class_name);

    // 获取子串的复数形式，例如：传参 `user` 会得到 `users`
    return str_plural($snake_case_name);
}