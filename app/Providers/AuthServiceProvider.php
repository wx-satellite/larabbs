<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Horizon\Horizon;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
		 \App\Models\Reply::class => \App\Policies\ReplyPolicy::class,
		 \App\Models\Topic::class => \App\Policies\TopicPolicy::class,
         User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //horizon默认只有在local环境下才能访问，如何动态的进行控制呢？
        // auth方法，返回true表示可以访问
        // 这里是将这个方法注册在AuthServiceProvider的boot方法中，因为它和授权相关，
        // 实际上这个方法也可以写在AppServiceProvider的boot方法中
        Horizon::auth(function(){
            // 只有站长才能访问
            return Auth::user()->hasRole("Founder");
        });
    }
}
