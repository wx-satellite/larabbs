<?php

namespace App\Observers;

use App\Handlers\SlugTranslateHandler;
use App\Models\Topic;

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


        // 如 slug 字段无内容，即使用翻译器对 title 进行翻译
        if(!$topic->slug) {
            $topic->slug = app(SlugTranslateHandler::class)->translate($topic->title);
        }
    }

//    public function updating(Topic $topic)
//    {
//        //XSS标签过滤，使用插件：composer require "mews/purifier:~2.0"
//        $topic->body = clean($topic->body,"user_topic_body");
//
//        $topic->excerpt = make_excerpt($topic->body);
//    }
}