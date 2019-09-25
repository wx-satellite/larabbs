<?php

namespace App\Handlers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Overtrue\Pinyin\Pinyin;



// 调用百度翻译的接口：一般第三方服务的验证信息都是存在services.php文件中的，使用env去获取。
// 使用env去获取是因为我们希望不同的环境有不同的值，因为".env"文件在不同的环境下是不一样的。
// 其次，我们需要把新增的env配置也追加到.env.example文件中：
/*      因为 .env 文件被我们排除 Git 跟踪（可以查看 .gitignore 文件），文件 .env.example 是作为项目环境变量的初始化文件而存在。
        当项目在新环境中安装时，只需要执行 cp .env.example .env 命令，并在 .env 填入对应的值，即可完成对项目环境变量的配置。
 */


/*
 *   释义的 URL 有助于搜索引擎优化（SEO），本章节我们将开发自动生成 SEO 友好 URL 的功能。
 *   当用户提交发布话题的表单时，程序将调用 百度翻译 接口将话题标题翻译为英文，并储存于字段 slug 中。
 *   显示时候将 Slug 在 URL 中体现出来，假如话题标题为『Slug 翻译测试』的 URL 是：
 *     http://larabbs.test/topics/119  加入slug后的seo友好链接：
 *     http://larabbs.test/topics/119/slug-translation-test
 */

class SlugTranslateHandler
{
    public function translate($text)
    {
        // 实例化 HTTP 客户端
        $http = new Client;

        // 初始化配置信息
        $api = 'http://api.fanyi.baidu.com/api/trans/vip/translate?';
        $appid = config('services.baidu_translate.appid');
        $key = config('services.baidu_translate.key');
        $salt = time();

        // 如果没有配置百度翻译，自动使用兼容的拼音方案
        if (empty($appid) || empty($key)) {
            return $this->pinyin($text);
        }

        // 根据文档，生成 sign
        // http://api.fanyi.baidu.com/api/trans/product/apidoc
        // appid+q+salt+密钥 的MD5值
        $sign = md5($appid. $text . $salt . $key);

        // 构建请求参数
        $query = http_build_query([
            "q"     =>  $text,
            "from"  => "zh",
            "to"    => "en",
            "appid" => $appid,
            "salt"  => $salt,
            "sign"  => $sign,
        ]);

        // 发送 HTTP Get 请求
        $response = $http->get($api.$query);

        $result = json_decode($response->getBody(), true);

        /**
        获取结果，如果请求成功，dd($result) 结果如下：

        array:3 [▼
        "from" => "zh"
        "to" => "en"
        "trans_result" => array:1 [▼
        0 => array:2 [▼
        "src" => "XSS 安全漏洞"
        "dst" => "XSS security vulnerability"
        ]
        ]
        ]

         **/


        //{"from":"zh","to":"en","trans_result":[{"src":"我喜欢你","dst":"I like you!"}]}
        // 尝试获取获取翻译结果
        if (isset($result['trans_result'][0]['dst'])) {
            return Str::slug($result['trans_result'][0]['dst']);
        } else {
            // 如果百度翻译没有结果，使用拼音作为后备计划。
            return $this->pinyin($text);
        }
    }

    public function pinyin($text)
    {
        return Str::slug(app(Pinyin::class)->permalink($text));
    }
}