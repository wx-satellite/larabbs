<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;

// MustVerifyEmailContract为接口类，实现该接口必须实现三个方法，
// 后续又use了MustVerifyEmailTrait这个Trait，这个Trait实现了上述接口需要实现的三个方法
class User extends Authenticatable implements MustVerifyEmailContract
{
    use Notifiable, MustVerifyEmailTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    // $fillable 属性的作用是防止用户随意修改模型数据，只有在此属性里定义的字段，才允许修改，否则更新时会被忽略。
    protected $fillable = [
        'name', 'email', 'password',"introduction","avatar"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    // $user->topics == $user->topics()->get()
    // $user->topics返回的是一个模型实例的集合，$user->topics()返回的是一个EloquentBuilder()
    public function topics() {
        return $this->hasMany(Topic::class,"user_id");
    }


    // 检测话题是不是当前用户的
    public function isAuthOf(Topic $topic) {
        return $this->id == $topic->user_id;
    }
}
