<?php

namespace App\Notifications;

use App\Models\Reply;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;


// 文档： https://learnku.com/docs/laravel/5.8/notifications/3921#database-prerequisites

// 消息的发送有两种方式：
//      使用trait的Notifiable的notify方法：$user->notify(new InvoicePaid($invoice));
//      使用Notification的Facade：Notification::send($users, new InvoicePaid($invoice));


//  大家应该会发现我们提交回复时，服务器响应会变得非常缓慢，
//  这是『邮件通知』功能请求了SMTP 服务器进行邮件发送所产生的延迟。
//  我们已经学过了，对于处理此类延迟，最好的方式是使用队列系统。
//  我们可以通过对通知类添加 ShouldQueue 接口和 Queueable trait 把通知加入队列。
class TopicReplied extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reply;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Reply $reply)
    {
        $this->reply = $reply;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */

    // 每个通知类都有个 via() 方法，它决定了通知在哪个频道上发送。我们写上 database 数据库来作为通知频道。
    public function via($notifiable)
    {
        return ['database',"mail"];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = $this->reply->topic->link(['#reply' . $this->reply->id]);
        return (new MailMessage)
                    ->line('你的话题有新回复！')
                    ->action('查看回复', $url);
    }


    // 因为使用数据库通知频道，我们需要定义 toDatabase()。(toArray()方法也是可以的)
    // 这个方法接收 $notifiable 实例参数并返回一个普通的 PHP 数组。
    // 这个返回的数组将被转成 JSON 格式并存储到通知数据表的 data 字段中。
    public function toDatabase($notifiable) {
        $topic = $this->reply->topic;
        $link =  $topic->link(['#reply' . $this->reply->id]);

        // 存入数据库里的数据
        return [
            'reply_id' => $this->reply->id,
            'reply_content' => $this->reply->content,
            'user_id' => $this->reply->user->id,
            'user_name' => $this->reply->user->name,
            'user_avatar' => $this->reply->user->avatar,
            'topic_link' => $link,
            'topic_id' => $topic->id,
            'topic_title' => $topic->title,
        ];
    }
}
