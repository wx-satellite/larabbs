<?php

namespace App\Models\Traits;



use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;


/*（在数据库中操作中，『写入』对数据库造成的压力，要远比『读取』压力高得多。）
 * 想要准确地跟踪用户的最后活跃时间，就必须在用户每一次请求服务器时都做记录，我们使用的数据库是 MySQL，
 * 也就是说每当用户访问一个页面，我们都向 MySQL 数据库里的 users 表写入数据。当我们有很多用户频繁访问站点时，这将会是数据库的一笔巨大开销。
 * 因此引入redis。Redis 运行在机器的内存上，读写效率都极快。
 * 不过为了保证数据的完整性，我们需要定期将 Redis 数据同步到数据库中，否则一旦 Redis 出问题或者执行了 Redis 清理操作，用户的『最后活跃时间』将会丢失。
 */

trait RecordLastActivedTime {


    protected $has_prefix = "larabbs_record_active_time_at";

    protected $field_prefix = "user_";


    protected function getHashPrefix($date) {
        return $this->has_prefix.$date;
    }

    protected function getFieldPrefix($uid) {
        return $this->field_prefix . $uid;

    }

    public function recordLastActivedAt() {

        $hash = $this->getHashPrefix(Carbon::now()->toDateString());

        $field = $this->getFieldPrefix($this->attributes["id"]);

        // 获取今天的日期加时间
        $time = Carbon::now()->toDateTimeString();

        // 存入redis
        Redis::hSet($hash, $field, $time);
    }


    // 将redis中的最近登陆时间同步到数据库
    public function syncUserActivedAt() {
        $hash = $this->getHashPrefix(Carbon::yesterday()->toDateString());
        // 获取昨天
        $data = Redis::hGetAll($hash);
        // 同步到数据库中，持久化
        foreach ($data as $key => $value) {
            // "user_1" 将会返回 "1"
            $uid = str_replace($this->field_prefix,"", $key);
            $user = User::query()->find($uid);
            if($user) {
                $user->last_active_time = $value;
                $user->save();
            }
        }
        Redis::del($hash);
    }

    // 获取时间：使用访问器，还有set开头的为修改器
    public function getLastActiveTimeAttribute($value) {
        $hash = $this->getHashPrefix(Carbon::now()->toDateString());
        $time = Redis::hGet($hash, $this->getFieldPrefix($this->attributes['id']));
        $time = $time?:$value;
        // 没有的话就取注册时间。
        return empty($time)?$this->created_at:Carbon::parse($time);
    }
}