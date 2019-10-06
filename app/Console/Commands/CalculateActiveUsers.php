<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;



/*
 * 在过去，开发者必须为每个需要调度的任务生成单独的 Cron 项目。然而令人头疼的是任务调度不受版本控制，并且需要 SSH 到服务器上来增加 Cron 条目。
Laravel 命令调度器允许你在 Laravel 中对命令调度进行清晰流畅的定义，并且仅需要在服务器上增加一条 Cron 项目即可。
调度在 app/Console/Kernel.php 文件的 schedule 方法中定义。在该方法内包含了一个简单的例子，你可以随意增加调度到 Schedule 对象中。

$ export EDITOR=vi && crontab -e
写入：* * * * * php /home/vagrant/code/larabbs/artisan schedule:run >> /dev/null 2>&1

 */
class CalculateActiveUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    // php artisan make:command --command=larabbs:calculate-active-users
    // 参数 --command 是指定 Artisan 调用的命令，一般情况下，我们推荐为命令加上命名空间，如本项目的"larabbs:"
    protected $signature = 'larabbs:calculate-active-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成活跃用户';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("开始计算......");
        (new User())->calculateAndCacheActiveUsers();
        $this->info("计算结束......");
    }
}
