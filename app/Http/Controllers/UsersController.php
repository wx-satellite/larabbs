<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\UserRequest;
use App\Models\User;


// 关于git的命令：
// git checkout .  撤销所有修改
// 对于新增的文件如何撤销，可以使用如下命令：
// git clean -f -d  命令 git clean 作用是清理项目，-f 是强制清理文件的设置，-d 选项命令连文件夹一并清除。


// 获取除了某一个参数以外的所有的参数值：
// request()->except("page") 或者 Illuminate\Support\Facades\Request::except("page")


// 关于表单验证：
// ["name"=>"required|min:2"] 和 ["name"=>["required","min:2"]] 等效


// 时间戳created_at和updated_at作为模型属性调用时会自动转成Carbon对象


// 对于数据库的初始化数据可以使用迁移进行初始化，命名格式： php artisan make:migration  _{数据表名称}_data
// 其次，在运行php artisan migrate时先运行迁移文件再运行数据填充文件

// 在空文件夹中放置 .gitkeep 保证了 Git 会将此文件夹纳入版本控制器中。

// 关于git rm -rf --cache的使用：https://blog.csdn.net/hobhunter/article/details/79463086


// 关于laravel的命令：
//  php artisan config:clear
//  php artisan config:cache  首先执行了php artisan config:clear再重新生成了配置文件缓存
//  php artisan route:cache
//  php artisan route::clear
//  php artisan cache:clear  清楚缓存，不影响生成的config配置文件缓存和路由缓存。
//参考文章：https://learnku.com/articles/4809/note-that-laravel-clears-cache-php-artisan-cacheclear-usage


// 关于安装第三方库的思考：网上有些教程在composer安装好第三方库之后会强调在app.php文件的providers数组中注册，但是本教程在安装好之后
// 没有强调添加到providers数组中，只是仅仅使用了"php artisan vendor:publish --provider==''"命令发布了就能使用。
// 问题参考文章：https://juejin.im/post/5affc6a051882542821c94a8


// 保证数据的一致性：
// 出现场景：例如我们删除用户，没有删除对应的话题，当我们进入话题之后获取用户的信息时就会报错。
// 解决方案：
//  1. 代码监听器：观察器的deleted方法删除对应的话题，好处是灵活、扩展性强，不受底层数据库约束，坏处是当删除时不添加监听器，就会出现漏删；
//  2. 数据库自带的外间约束：好处是数据一致性强，基本上不会出现漏删，坏处是有些数据库不支持，如 SQLite。




class UsersController extends Controller
{

    public function __construct()
    {
        // 除了show方法其他都需要登陆才能访问，如果不登陆访问会跳转到登陆页面，具体可以查看auth中间件
        // except类似黑名单机制
        $this->middleware("auth",["except"=>["show"]]);
    }

    //当请求 http://larabbs.test/users/1 Laravel 将会自动查找 ID 为 1 的用户并赋值到变量 $user 中，
    //如果数据库中找不到对应的模型实例，会自动生成 HTTP 404 响应，
    public function show(User $user) {
        return view("users.show",compact("user"));
    }


    public function edit(User $user) {
        // 授权策略：授权失败时会返回403禁止访问
        $this->authorize("update", $user);
        return view("users.edit",compact("user"));
    }


    // 使用表单请求验证：只有当验证通过时，才会执行 控制器 update() 方法中的代码。否则抛出异常，并重定向至上一个页面，附带验证失败的信息。
    // 会自动判断请求返回值需要的类型，例如当AJAX 的请求时，Laravel 并不会生成一个重定向响应，而是会生成一个包含所有验证错误信息的 JSON 响应。这个 JSON 响应会包含一个 HTTP 状态码 422 被发送出去
    // 同理：使用$request->validate()和$this->validate()也是符合上面的规则。
    // 但是使用Validator::make这种方式创建，则不符合上述规则。
    // 这时候如果你还想手动创建验证器实例，又想使用 validates 方法提供的自动重定向，
    // 那么你可以在现有的验证器示例上调用 validate 方法。如果验证失败，用户将会自动重定向。在 AJAX 请求中，则会返回 JSON 格式的响应。
    // 例如：Validator::make($request->all(), ['title' => 'required|unique:posts|max:255'])->validate();
    public function update(User $user, UserRequest $request, ImageUploadHandler $handler) {
        $this->authorize("update",$user);
        $data = $request->all();


        // 获取上传的文件两种方式都可以：
        //      $request->file("avatar")
        //      $request->avatar
        // 注意表单上传文件时记得加上：enctype="multipart/form-data"
        if($request->avatar) {

            //  裁剪：https://learnku.com/courses/laravel-intermediate-training/5.8/avatar-croping/4157
            $res = $handler->save($request->avatar,"avatar", $user->id, 416);
            if($res) {
                $data["avatar"] = $res["path"];
            }
        }

        // 即使这边将请求的所有参数都传递给update，但是update只会更新fillable填充的字段
        $user->update($data);




        // with函数等效于：session()->flash("success","个人资料更新成功！")
        return redirect()->route("users.show",[$user])->with("success","个人资料更新成功！");
    }
}
