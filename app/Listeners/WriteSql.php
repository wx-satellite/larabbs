<?php

namespace App\Listeners;

use DateTime;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class WriteSql
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  QueryExecuted  $event
     * @return void
     */
    public function handle(QueryExecuted $event)
    {
        if(env("APP_DEBUG", true)) {
            $sql = str_replace("?", "'%s'", $event->sql);
            foreach ($event->bindings as $i => $binding) {
                if ($binding instanceof DateTime) {
                    $event->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                } else {
                    if (is_string($binding)) {
                        $event->bindings[$i] = "'$binding'";
                    }
                }
            }
            if($event->bindings) {
                // 前者传入是多个参数，后者传入的是一个数组
                // sprintf("it is a %s %s","name","wx");
                // vsprintf("it is a %s %s", ["name","wx"]);
                $sql = vsprintf($sql, $event->bindings);
            }
            $filename = 'laravel-'.date("Y-m-d", time()).'.log';
            $filePath = storage_path("logs/{$filename}");
            if(!file_exists($filePath)) {
                fopen($filename, "a+");
            }
            file_put_contents($filePath, '[' . date('Y-m-d H:i:s') . '] '.$sql."\r\n",FILE_APPEND);
        }
    }
}
