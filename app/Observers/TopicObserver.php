<?php

namespace App\Observers;

use App\Handlers\SlugTranslateHandler;
use App\Jobs\TranslateSlug;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

//  当一个新模型被初次保存将会触发 creating 以及 created 事件。
//  如果一个模型已经存在于数据库且调用了 save 方法，将会触发 updating 和 updated 事件。
//  在这两种情况下都会触发 saving 和 saved 事件。

class TopicObserver
{
    public function saving(Topic $topic)
    {
        // XSS标签过滤，使用插件：composer require "mews/purifier:~2.0"
        $topic->body = clean($topic->body,"user_topic_body");

        $topic->excerpt = make_excerpt($topic->body);


//        // 如 slug 字段无内容，即使用翻译器对 title 进行翻译
//        if(!$topic->slug) {
//            dispatch(new TranslateSlug($topic));
//        }
    }


    // 在job类的构造函数中传入的Eloquent模型实例序列化的时候只会序列化ID字段，
    // 如果放在saving方法中的话这个ID可能为空导致job执行失败
    public function saved(Topic $topic)
    {
        // 如 slug 字段无内容，即使用翻译器对 title 进行翻译
        if ( ! $topic->slug) {

            // 推送任务到队列
            dispatch(new TranslateSlug($topic));
        }
    }

//    public function updating(Topic $topic)
//    {
//        //XSS标签过滤，使用插件：composer require "mews/purifier:~2.0"
//        $topic->body = clean($topic->body,"user_topic_body");
//
//        $topic->excerpt = make_excerpt($topic->body);
//    }



    // 新增了 deleted() 方法来监控话题成功删除的事件。
    // 需注意，在模型监听器中，数据库操作需避免再次触发 Eloquent 事件，以免造成联动逻辑冲突。所以这里我们使用了 DB 类进行操作。
    // 避免出现：
    //  如果使用模型删除的话会触发reply的删除事件，这时候会去更新对应topic的reply_count字段，但是这时候topic对象已经被删除了。会报错
    public function deleted(Topic $topic) {

        DB::table("replies")->where("topic_id", $topic->id)->delete();
    }
}