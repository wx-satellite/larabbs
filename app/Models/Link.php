<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;


// 创建模型的同时创建迁移：php artisan make:model Models/Link -m
class Link extends Model
{

    protected $fillable = ["link","title"];


    // 缓存配置
    public  $cache_key = "larabbs_key";
    protected $expire_time = 1440 * 60;

    public function getLinksFromCache() {
        return Cache::remember($this->cache_key, $this->expire_time, function(){
            return $this->all();
        });
    }
}
