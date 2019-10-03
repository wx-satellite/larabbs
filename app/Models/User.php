<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

// MustVerifyEmailContract为接口类，实现该接口必须实现三个方法，
// 后续又use了MustVerifyEmailTrait这个Trait，这个Trait实现了上述接口需要实现的三个方法
class User extends Authenticatable implements MustVerifyEmailContract
{
    use Notifiable, MustVerifyEmailTrait;

    // 这个trait可以让我们使用扩展包中提供的权限角色方法
    use HasRoles;

    // 标记通知消息已读
    public function markAsRead() {
        $this->notification_count = 0;
        $this->save();
        //文档：https://learnku.com/docs/laravel/5.8/notifications/3921#marking-notifications-as-read
        // 注意这里是$this->unreadNotifications而不是$this->unreadNotifications()，后者得到的是一个QueryBuilder不能调用markAsRead方法
        $this->unreadNotifications->markAsRead();
    }

    public function topicNotify($notification) {
        // 如果要通知的用户是当前登陆的用户就不做任何处理
        if($this->id == Auth::id()) {
            return;
        }
        $this->increment("notification_count",1);
        $this->notify($notification);
    }
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

    public function replies() {
        return $this->hasMany(Reply::class,"topic_id");
    }


    // 检测话题是不是当前用户的
    public function isAuthOf(Topic $topic) {
        return $this->id == $topic->user_id;
    }


    // 修改器
    // 访问器和修改器最大的区别是『发生修改的时机』，访问器是 访问属性时 修改，修改器是在 写入数据库前 修改。修改器是数据持久化，访问器是临时修改。
    public function setPasswordAttribute($value) {
        if (strlen($value) != 60) {

            // 不等于 60，做密码加密处理
//            $value = bcrypt($value);
            $value = Hash::make($value);
        }
        $this->attributes["password"] = $value;
    }

    public function setAvatarAttribute($path)
    {
        // 如果不是 `http` 子串开头，那就是从后台上传的，需要补全 URL
        if ( ! Str::startsWith($path, 'http')) {

            // 拼接完整的 URL
            $path = config('app.url') . "/uploads/images/avatar/$path";
        }

        $this->attributes['avatar'] = $path;
    }
}
