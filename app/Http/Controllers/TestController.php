<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Overtrue\Pinyin\Pinyin;

class TestController extends Controller
{

    public function test(Request $request) {
        var_dump($request->name);
        var_dump(Str::slug("i-am-wei-xin"));
        var_dump(Str::slug("my name is weixin"));
        var_dump(app(Pinyin::class)->permalink("我爱你"));
        var_dump(route("topics.show",[1,"my-name-is-weixin","name"=>"weixin","age"=>12]));
    }

    public function show() {
        dd(11);
    }
}
