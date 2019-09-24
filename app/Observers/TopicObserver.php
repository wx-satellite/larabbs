<?php

namespace App\Observers;

use App\Models\Topic;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored


class TopicObserver
{
    public function creating(Topic $topic)
    {
        // XSS标签过滤，使用插件：composer require "mews/purifier:~2.0"
        $topic->body = clean($topic->body,"user_topic_body");

        $topic->excerpt = make_excerpt($topic->body);
    }

    public function updating(Topic $topic)
    {
        //
    }
}