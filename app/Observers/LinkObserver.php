<?php

namespace App\Observers;

use App\Models\Link;
use Illuminate\Support\Facades\Cache;


// php artisan make:observer LinkObserver --model=Models/Link
// 创建好模型观察器之后，记得在AppServiceProvider中注册。

class LinkObserver
{
    // 因为我们对资源做了缓存，那么当资源发生改变时，我们需要刷新缓存
    public function saved(link $link) {
        Cache::forget($link->cache_key);
    }
    /**
     * Handle the link "created" event.
     *
     * @param  \App\Models\Link  $link
     * @return void
     */
    public function created(Link $link)
    {
        //
    }

    /**
     * Handle the link "updated" event.
     *
     * @param  \App\Models\Link  $link
     * @return void
     */
    public function updated(Link $link)
    {
        //
    }

    /**
     * Handle the link "deleted" event.
     *
     * @param  \App\Models\Link  $link
     * @return void
     */
    public function deleted(Link $link)
    {
        //
    }

    /**
     * Handle the link "restored" event.
     *
     * @param  \App\Models\Link  $link
     * @return void
     */
    public function restored(Link $link)
    {
        //
    }

    /**
     * Handle the link "force deleted" event.
     *
     * @param  \App\Models\Link  $link
     * @return void
     */
    public function forceDeleted(Link $link)
    {
        //
    }


}
