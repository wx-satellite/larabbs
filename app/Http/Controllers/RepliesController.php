<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReplyRequest;
use Illuminate\Support\Facades\Auth;

class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }


    // 永远不要相信用户提交的数据：因此需要对用户提交的数据验证。
    // 目前位置验证的方式有如下几种：
    //      1.  使用表单请求验证
    //      2.  直接使用控制器的方法：$this->validate
    //      3.  构造验证类: \Validation::make()
	public function store(ReplyRequest $request, Reply $reply)
	{
        $reply->content = request()->input("content");
        $reply->user_id = Auth::id();
        $reply->topic_id = $request->topic_id;
        $reply->save();

        return redirect()->to($reply->topic->link())->with('success', '评论创建成功！');
	}


	public function destroy(Reply $reply)
	{
		$this->authorize('destroy', $reply);
		$reply->delete();

		return redirect()->route('replies.index')->with('message', 'Deleted successfully.');
	}
}