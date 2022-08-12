# LaravelLib

LaravelLib 是`Laravel`专用个人使用包

[使用文档](https://qilindao.github.io/docs/backend/laravel-lib/index.html)

## 环境需求

- PHP ≥ 7.0
- Laravel ≥ 6.x

## 安装

```shell
composer require joycezhang/laravellib
```

## 生成配置文件

```shell
php artisan vendor:publish --provider="JoyceZ\LaravelLib\ServiceProvider"
```

## 命名空间

`\\JoyceZ\\LaravelLib\\`

## Config 配置文件

> 👋🏼 您当前浏览的文档为 2.x

```php
return [
    'passport' => [//登录配置
        'check_captcha_cache_key' => 'captcha_uniqid',//图形key
        'password_salt' => env('LANDAO_PASSPORT_PASSWORD_SALT', env('APP_KEY'))//密码加密salt
    ],
    'crypt' => [//数据库可逆加密
        'screct_key' => env('LANDAO_CRYPT_SCRECT_KEY', env('APP_KEY'))
    ],
    'captcha' => [//图形验证码
        'charset' => 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789',
        'codelen' => 4,
        'width' => 130,
        'height' => 50,
        // 为空为默认字体
        'font' => '',
        'fontsize' => 20,
        'cachetime' => 300,
    ],
    'paginate' => [//页数
        'page_size' => 20
    ],
    'generator' => [//Artisan 命令生成 Repository 和 enum 相关文件的指定根目录
        'basePath' => app()->path(),
        'rootNamespace' => 'App\\',
        'paths' => [
            'models' => 'Services\\Models',//model 生成根目录
            'repositories' => 'Services\\Repositories',//repository 生成根目录
            'interfaces' => 'Services\\Repositories',//repository interfaces 生成根目录。实际在生成中会转成 'App\Services\Repositories\Bolg\Interfaces\IPost.php
            'enums' => 'Services\\Enums',
        ]
    ]
];

```

## Artisan 命令生成

> 👋🏼 您当前浏览的文档为 2.x

 根目录配置，参见[Config 配置文件](#config-配置文件)

::: warning 提示
Artisan 命令生成的文件，会根据具体类型更改文件名：`PostEnum`、`PostModel`、`IPost`、`PostRepo`
:::

## Repository 命令生成

```shell
php artisan make:repository "Blog\Post"
```
执行以上命名会生成三个对应的文件如下

- `App\Services\Models\Bolg\PostModel.php`
- `App\Services\Repositories\Bolg\Interfaces\IPost.php`
- `App\Services\Repositories\Bolg\PostReop.php`



## Enum 命令生成


```shell
php artisan make:enum "Blog\Post"
```

运行以上命令生成的文件结构为`App\Services\Enums\Bolg\PostEnum.php`

