<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Models\Category;
use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Requests\TopicRequest;
use Illuminate\Support\Facades\Auth;


// 代码脚手架包： composer require "summerblue/generator:~1.0" --dev
// 执行php artisan make:scaffold Projects --schema xxxx


// composer require "barryvdh/laravel-debugbar:~3.2" --dev   安装性能分析包（开发者工具类）
// php artisan vendor:publish --provider="Barryvdh\Debugbar\ServiceProvider"


/*
 * php artisan migrate:refresh --seed 回滚所有迁移并重新执行migrate，--seed参数表示会同时运行db:seed命令
 * 注意事项：
 * 注：开发时尽量不要手动往数据库里写入内容，因为类似于 migrate:refresh 这种操作是很频繁的，
 * 如果你想要数据库里有一些数据，请使用数据填充功能。这样做除了能被纳入版本控制以外，另一个好处是能让你不需要依赖数据库里的数据，
 * 这在团队协作中尤其重要，因为队友很多时候不是和你使用同一个数据库。希望同学们尽早养成好习惯。
 *
 */


/*
 * class Dog {
 *      protected $attributes = ["name"=>"weixin"];
 *      public function __set($name, $value) {
 *          $this->attributes[$name]=$value;
 *      }
 *      public function __get($name){
 *          return $this->attributes[$name];
 *      }
 * }
 *   $dog = new Dog();
 *
 *   判断了对象是否有name属性：
 *      empty($dog->name);  为true
 *
 *   通过__get魔术方法获得了name的值：
 *      $name = $dog->name;
 *      empty($name); 为false;
 */


class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    //{!! $topics->appends(Request::except('page'))->render() !!}表示：除了page参数以外其他的参数都追加到分页链接中
	public function index(Request $request,$pageSize=30)
	{
	    // 关联模型预加载：解决了N+1的问题
		$topics = Topic::query()
            ->withOrder($request->order)
            ->paginate($pageSize);
		return view('topics.index', compact('topics'));
	}


	// 需求：只有当前用户登陆了，才引入评论回复框
    //      常见的方式就是@if(Auth::check()) 再决定引入不引入
    //      另一种方式可以使用@includeWhen(Auth::check(),"topics._reply_box",["topic"=>$topic])
    public function show(Request $request,Topic $topic)
    {
        // 注意：路由绑定的参数，通过$request->slug也是可以获取到的。这种方式不仅可以获取到表单提交的数据连上传的文件数据也能这么获取
        // URL矫正
        if($topic->slug && $topic->slug != $request->slug ) {
            return redirect($topic->link(),301);
        }
        return view('topics.show', compact('topic'));
    }

	public function create(Topic $topic)
	{
	    $categories = Category::query()->get();
		return view('topics.create_and_edit', compact('topic','categories'));
	}


	// 1. 第二个参数会创建一个空白的Topic对象
    // 2. 模型的观察器：https://learnku.com/docs/laravel/5.8/eloquent/3931#observers（模型观察器其实可以用模型事件代替）
    // 3. 不要相信任何用户输入的数据，可能会触发XSS攻击：https://learnku.com/courses/laravel-intermediate-training/5.8/safety-problem/4174
    //      XSS 也称跨站脚本攻击 (Cross Site Scripting)，恶意攻击者往 Web 页面里插入恶意 JavaScript 代码，当用户浏览该页之时，嵌入其中 Web 里面的 JavaScript 代码会被执行，从而达到恶意攻击用户的目的。
    //    解决方案：1. 对用户提交的数据进行过滤  2. 在网页显示的时候进行转义，一般使用htmlspecialchars输出
    //    在laravel中{{}}自动会调用htmlspecialchars，而{!! !!}则是原样输出
    // 对用户的输入进行过滤可以使用： composer require "mews/purifier:~2.0"  这个包

	public function store(TopicRequest $request, Topic $topic)
	{
	    // 该方法和Topic::query()->create()相似，只会填充fillable属性指定的字段
		$topic->fill($request->all());
		$topic->user_id = Auth::id();
		$topic->save();
		return redirect()->to($topic->link())->with('success', '帖子创建成功！');
	}

	public function edit(Topic $topic)
	{
        // 在授权策略的类方法里，返回 true 即允许访问，反之返回 false 为拒绝访问。
        $this->authorize('update', $topic);
        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic',"categories"));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());
		return redirect()->to($topic->link())->with('success', '帖子编辑成功！');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();
		return redirect()->route('topics.index')->with('success', '删除帖子成功！');
	}

	// 图片上传：在laravel的控制器中如果直接返回数组会被解析成json
    public function uploadImage(Request $request, ImageUploadHandler $handler) {

        // 初始化
        $response = [
            "success" => false,
            "message" => "上传失败！",
            "file_path" => ""
        ];
        // 如果有文件上传
        if($request->upload_file) {
            $res = $handler->save($request->upload_file,"topics",Auth::id(), 1024);
            if($res) {
                $response["success"] = true;
                $response["file_path"] = $res["path"];
                $response["message"] = "上传成功！";
            }
        }
        return $response;
    }
}