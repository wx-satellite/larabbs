<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


// php artisan make:model Models/Category -m   参数m表示顺便创建数据库迁移文件

// 迁移文件不仅可以创建表或者修改表结构，还可以用于生产环境数据的初始化。例如：seed_categories_data
// 格式："seed_{table}_data"

// 我们要确保数据初始化迁移文件在表创建迁移文件之后！
// 注意laravel的数据填充seed填充的是假数据，不适合当前这个场景
class Category extends Model
{
    //

    protected $fillable = ["name","description"];
}
