<?php

namespace App\Models;

class Topic extends Model
{

    /*
     *  title	帖子标题	字符串（String）	文章搜索	无
        body	帖子内容	文本（text）	不需要	无
        user_id	用户 ID	整数（int）	数据关联	unsigned()
        category_id	分类 ID	整数（int）	数据关联	unsigned()
        reply_count	回复数量	整数（int）	文章回复数量排序	unsigned(), default(0)
        view_count	查看总数	整数（int）	文章查看数量排序	unsigned(), default(0)
        last_reply_user_id	最后回复的用户 ID	整数（int）	数据关联	unsigned(), default(0)
        order	可用来做排序使用	整数（int）	不需要	default(0)
        excerpt	文章摘要，SEO 优化时使用	文本（text）	不需要	nullable()
        slug	SEO 友好的 URI	字符串（String）	不需要
     */
    protected $fillable = ['title', 'body', 'user_id', 'category_id', 'reply_count', 'view_count', 'last_reply_user_id', 'order', 'excerpt', 'slug'];

    public function user() {
        return $this->belongsTo(User::class,"user_id");
    }

    public function category() {
        return $this->belongsTo(Category::class,"category_id");
    }


    // 本地作用域

    public function scopeRecent($query)
    {
        // 按照创建时间来排序
        return $query->orderBy("created_at","desc");
    }

    public function scopeRecentReplied($query) {
        // 按照最近回复时间来排序
        return $query->orderBy("updated_at","desc");
    }

    public function scopeWithOrder($query,$order) {
        switch ($order){
            case "recent":
                $query->recent();
                break;
            default:
                $query->recentReplied();
                break;
        }
        // 预加载，with可以传递一个数组或者多个参数，下面的代码等价于$query->with(["user","category"]);
        // func_get_args()获取一个函数的所有参数
        return $query->with("category","user");
    }
}
