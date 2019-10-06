<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Link;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{


    // 注意：Illuminate\Http\Request和Illuminate\Support\Facades\Request是不同的类，后者的instance()方法获得前者的实例

    public function show(Category $category, Request $request,$pageSize=15) {
        // 取出当前分类的所有文章
        $topics = Topic::query()
            ->where("category_id", $category->id)
            ->withOrder($request->order)
            ->paginate($pageSize);
        $active_users = (new User())->getActiveUsers();
        $links = (new Link())->getLinksFromCache();
        return view("topics.index",compact("topics","category",'active_users','links'));
    }
}
