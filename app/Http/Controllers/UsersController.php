<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\UserRequest;
use App\Models\User;



// 时间戳created_at和updated_at作为模型属性调用时会自动转成Carbon对象
class UsersController extends Controller
{

    //当请求 http://larabbs.test/users/1 Laravel 将会自动查找 ID 为 1 的用户并赋值到变量 $user 中，
    //如果数据库中找不到对应的模型实例，会自动生成 HTTP 404 响应，
    public function show(User $user) {
        return view("users.show",compact("user"));
    }


    public function edit(User $user) {
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

        $data = $request->all();


        // 获取上传的文件两种方式都可以：
        //      $request->file("avatar")
        //      $request->avatar
        // 注意表单上传文件时记得加上：enctype="multipart/form-data"
        if($request->avatar) {
            $res = $handler->save($request->avatar,"avatar", $user->id);
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
