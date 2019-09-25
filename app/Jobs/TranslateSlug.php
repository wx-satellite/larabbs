<?php

namespace App\Jobs;

use App\Handlers\SlugTranslateHandler;
use App\Models\Topic;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;


//为什么要使用队列：队列允许你异步执行消耗时间的任务，比如请求一个 API 并等待返回的结果。




// https://learnku.com/laravel/t/13290/excitement-first-contact-with-the-queue-take-a-look-at-your-mind-and-add-two-questions
// 注意将队列驱动改成redis时记得开启redis服务，在Homestead中默认是开启redis服务的
// phpredis包和predis包的区别：前者需要安装php的redis的c扩展，后者是纯php开发的客户端，性能方面是前者优秀



//该类实现了 Illuminate\Contracts\Queue\ShouldQueue 接口，该接口表明 Laravel 应该将该任务添加到后台的任务队列中，而不是同步执行。
class TranslateSlug implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $topic;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    //引入了 SerializesModels trait，Eloquent 模型会被优雅的序列化和反序列化。
    //队列任务构造器中接收了 Eloquent 模型，将会只序列化模型的 ID。
    public function __construct(Topic $topic)
    {
        // 队列任务构造器中接收了 Eloquent 模型，将会只序列化模型的 ID
        $this->topic = $topic;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        // 请求百度 API 接口进行翻译
        $slug = app(SlugTranslateHandler::class)->translate($this->topic->title);

        // 为了避免模型监控器死循环调用，我们使用 DB 类直接对数据库进行操作
        /*
         * 我们将会在模型监控器中分发任务，任务中要避免使用 Eloquent 模型接口调用，如：create(), update(), save() 等操作。
         * 否则会陷入调用死循环 —— 模型监控器分发任务，任务触发模型监控器，
         * 模型监控器再次分发任务，任务再次触发模型监控器.... 死循环。
         * 在这种情况下，使用 DB 类直接对数据库进行操作即可。
         */
        DB::table('topics')->where('id', $this->topic->id)->update(['slug' => $slug]);
    }
}
