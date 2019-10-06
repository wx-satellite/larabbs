<?php



/*
 *
SELECT a.id, (b.topic_count + c.reply_count) as activite FROM users a LEFT JOIN
(SELECT user_id, (count(*) * 4) as topic_count FROM topics  GROUP BY user_id) b ON a.id = b.user_id
LEFT JOIN
(SELECT user_id, count(*) as reply_count FROM replies GROUP BY user_id) c ON a.id = c.user_id
ORDER BY activite DESC

SELECT count(*) FROM topics WHERE user_id = 1
SELECT count(*) FROM replies WHERE user_id = 1
 */

namespace App\Models\Traits;


// 使用 Trait 的加载方式既让相关的方法都存放于一处，便于查阅，另一方面，也保持了模型的清爽。
trait ActiveUserHelp {

    protected $users = [];

    // 配置信息
    protected $topic_weight = 4;
    protected $reply_weight = 1;
    protected $pass_day = 7;
    protected $user_number = 6;

    // 缓存配置
    protected $cache_key = "larabbs_active_users";
    protected $expire_time = 65 * 60;


    // 获取活跃用户
    public function getActiveUsers() {
        // 尝试从缓存中取出 cache_key 对应的数据。如果能取到，便直接返回数据。
        // 否则运行匿名函数中的代码来取出活跃用户数据，返回的同时做了缓存。
        return \Illuminate\Support\Facades\Cache::remember($this->cache_key, $this->expire_time,function (){
            return $this->calculateActiveUsers();
        });
    }

    public function calculateAndCacheActiveUsers() {
        $activeUsers = $this->calculateActiveUsers();
        $this->cacheActiveUsers($activeUsers);
    }


    protected function calculateActiveUsers() {
        $filter_time = \Illuminate\Support\Carbon::now()->subDays($this->pass_day);
        $users = \Illuminate\Support\Facades\DB::select("SELECT a.*, (b.topic_count + c.reply_count) as active 
FROM users a LEFT JOIN
(SELECT user_id, (count(*) * {$this->topic_weight}) as topic_count FROM topics WHERE created_at >= '{$filter_time}' GROUP BY user_id ) b ON a.id = b.user_id
LEFT JOIN
(SELECT user_id, (count(*) * {$this->reply_weight}) as reply_count FROM replies WHERE created_at >= '{$filter_time}' GROUP BY user_id ) c ON a.id = c.user_id
ORDER BY active DESC LIMIT {$this->user_number}");
        return $users;
    }

    protected function cacheActiveUsers($activeUsers) {
        // 将数据放入缓存中
        \Illuminate\Support\Facades\Cache::put($this->cache_key, $activeUsers, $this->expire_time);
    }
}