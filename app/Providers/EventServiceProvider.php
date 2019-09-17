<?php

namespace App\Providers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [

        // 当用户注册成功后会触发一个Registered的事件
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],


        // 需求：希望在验证成功之后闪存一条消息，但是直接修改源码是不好的，因为无法纳入版本库一composer之后就恢复原来的样子了
        // 查看源码发现验证成功之后会触发一个Verified事件，这时候监听这个事件即可（解耦）
        Verified::class => [
            \App\Listeners\EmailVerified::class
        ],

        // 需求：修改密码成功默认没有提示（类似于上面邮件认证成功），因此需要闪存一条消息。
        // 修改密码成功，会触发一个：PasswordReset事件，监听这个事件就可以闪存消息
        // 当然还有一种解决方案：在ResetPasswordController控制器中重写sendResetResponse这个方法
        PasswordReset::class => [
            \App\Listeners\MessagePasswordReset::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
