<?php

namespace App\Observers;

use App\Models\Reply;
use App\Notifications\TopicReplied;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ReplyObserver
{
    public function creating(Reply $reply)
    {
        //
        $reply->content = clean($reply->content, "user_topic_body");
    }

    public function updating(Reply $reply)
    {
        //
    }

    // 回复被创建时，需要在对应话题的统计字段上加1
    public function created(Reply $reply) {
//        $reply->topic()->increment("reply_count",1);

        // 上面自增 1 是比较直接的做法。
        // 另一个比较严谨的做法是创建成功后计算本话题下评论总数，然后在对其 reply_count 字段进行赋值。
        // 这样做的好处多多，一般在做 xxx_count 此类总数缓存字段时，推荐使用此方法：
        $reply->topic->updateReplyCount();

        // 通知话题的用户，话题有了新的回复。
        // 教程：https://learnku.com/courses/laravel-intermediate-training/5.8/message-notification/4183
        // 重写了User模型的notify方法（如果是发送给自己的通知，就不会发送了，但是激活邮件是需要发送给自己的。因此会导致框架自带的「重新发送邮件」的功能失效）
        // 这里就新开启一个方法。
        $reply->topic->user->topicNotify(new TopicReplied($reply));

    }


    // 回复被删除时
    public function deleted(Reply $reply) {
        $reply->topic->updateReplyCount();
    }
}