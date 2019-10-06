<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;


/*
 *  前置中间件是应用初始化完成以后立刻执行，此时控制器路由还未分配、控制器还未执行、视图还未渲染。
 *      public function handle($request, Closure $next)
        {
            // 这是前置中间件，在还未进入 $next 之前调用

            return $next($request);
        }
 *  后置中间件是即将离开应用的响应，此时控制器已将渲染好的视图返回，我们可以在后置中间件里修改响应。
 *      public function handle($request, Closure $next)
        {
            $response = $next($request);

            // 这是后置中间件，$next 已经执行完毕并返回响应 $response，
            // 我们可以在此处对响应进行修改。

            return $response;
        }
 */
class RecordLastActivedTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::check()) {
            Auth::user()->recordLastActivedAt();
        }
        return $next($request);
    }
}
