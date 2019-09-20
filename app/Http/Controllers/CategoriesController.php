<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class CategoriesController extends Controller
{
    //

    public function show(Category $category, $pageSize=15) {
        // 取出当前分类的所有文章
        $topics = Topic::query()->where("category_id", $category->id)->with("category")->paginate($pageSize);

        return view("topics.index",compact("topics","category"));
    }
}
