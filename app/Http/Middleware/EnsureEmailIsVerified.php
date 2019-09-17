<?php

namespace App\Http\Middleware;

use Closure;


// 当用户登陆了并且邮箱未激活并且除了访问"email/*"和"/logout"之外，其他都重定向到邮箱验证页面
// abort方法会根据请求需要的结果（header 里有 accept: application/json)动态调整返回的结果格式。
class EnsureEmailIsVerified
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
        if($request->user() && !$request->user()->hasVerifiedEmail() && !$request->is("email/*", "logout")) {
            return $request->expectsJson() ? abort(403,"Your email address is not verified!"):redirect()->route("verification.notice");
        }
        return $next($request);
    }
}
