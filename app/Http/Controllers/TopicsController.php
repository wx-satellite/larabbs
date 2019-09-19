<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;



// 代码脚手架包： composer require "summerblue/generator:~1.0" --dev
// 执行php artisan make:scaffold Projects --schema xxxx


// composer require "barryvdh/laravel-debugbar:~3.2" --dev   安装性能分析包（开发者工具类）
// php artisan vendor:publish --provider="Barryvdh\Debugbar\ServiceProvider"



class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    //{!! $topics->appends(Request::except('page'))->render() !!}表示：除了page参数以外其他的参数都追加到分页链接中
	public function index($pageSize=30)
	{
	    // 关联模型预加载：解决了N+1的问题
		$topics = Topic::query()->with(["user","category"])->paginate($pageSize);
		return view('topics.index', compact('topics'));
	}

    public function show(Topic $topic)
    {
        return view('topics.show', compact('topic'));
    }

	public function create(Topic $topic)
	{
		return view('topics.create_and_edit', compact('topic'));
	}

	public function store(TopicRequest $request)
	{
		$topic = Topic::query()->create($request->all());
		return redirect()->route('topics.show', $topic->id)->with('message', 'Created successfully.');
	}

	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);
		return view('topics.create_and_edit', compact('topic'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

		return redirect()->route('topics.show', $topic->id)->with('message', 'Updated successfully.');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('message', 'Deleted successfully.');
	}
}