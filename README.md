# 常用的一些扩展类库

## 依赖

1. PHP >=7.1
2. Composer
3. Laravel 6+

## 命名空间

> `\\JoyceZ\\LaravelLib\\`

## 安装

> `composer require joycezhang/laravellib`

## 生成配置文件

> `php artisan vendor:publish --provider="JoyceZ\LaravelLib\ServiceProvider"`

## 用法

### Repositories 逻辑容器仓库设计模式

需要在服务容器`AppServiceProvider`类 `register()` 中绑定 `Repositories`

` $this->app->bind(IManage::class, ManageRepo::class);//管理员 `

建议新建一个服务类 `RepositoryServiceProvider` 用于专门管理`Repositories`服务的绑定

1. 编写服务提供者

```yaml
php artisan make:provider RepositoryServiceProvider
```

2. 注册服务提供者

服务提供者都是通过配置文件 `config/app.php` 进行注册，只需要将服务添加到 `providers`数组中
```php

'providers' => [
    // 其他服务提供者

    App\Providers\RepositoryServiceProvider::class,
]

```

#### Interfaces 接口继承

```php
namespace App\Services\Repositories\Manage\Interfaces;


use JoyceZ\LaravelLib\Repositories\Interfaces\BaseInterface;

/**
 * 管理员
 * Interface IManage
 * @package App\Services\Repositories\Manage\Interfaces
 */
interface IManage extends BaseInterface
{

}
```
#### 实现 Interfaces 接口继承
```php

namespace App\Services\Repositories\Manage;


use App\Services\Models\Manage\ManageModel;
use App\Services\Repositories\Manage\Interfaces\IManage;
use JoyceZ\LaravelLib\Repositories\BaseRepository;

/**
 * 管理员
 * Class ManageRepo
 * @package App\Services\Repositories\Manage
 */
class ManageRepo extends BaseRepository implements IManage
{

    public function __construct(ManageModel $model)
    {
        parent::__construct($model);
    }
}
```
##### Repositories 内置接口方法

> 注意事项：数据层，模型中要指定主键名称，部分实现的接口是直接通过模型设置指定的主键名称来操作数据层

###### 根据主键id获取单条数据

```php
/**
 * 根据主键id获取单条数据
 * @param int $id 主键id
 * @return mixed
 */
public function getByPkId(int $id);

```

###### 根据条件，获取一条指定字段数据

```php
/**
 * 根据条件，获取一条指定字段数据
 * @param array $columns  要查询字段
 * @param array $condition 查询条件
 * @return mixed
 */
public function first(array $condition, array $columns = ['*']);
```

###### 没有查找到单条数据，抛出异常

```php
/**
* 没有查找到数据，抛出异常
* @param array $condition 查询条件
* @return mixed
*/
public function findOneOrFail(array $condition);
```

###### 获取全部数据

```php
/**
 * 获取全部数据，不支持链表查询
 * @param array $condition 查询条件
 * @param array $columns 显示字段
 * @param string $orderBy
 * @param string $sortBy
 * @return mixed
 */
public function all(array $condition = [], $columns = ['*'], string $orderBy = 'id', string $sortBy = 'asc');

```
###### 创建一条数据，不联表状态
```php
/**
 * 创建一条数据，不联表状态
 * @param array $attributes
 * @return mixed
 */
public function doCreate(array $attributes);
```
###### 根据主键id，更新一条数据
```php
/**
 * 根据主键id，更新一条数据,模型要设定指定主键id
 * @param array $attributes 要更新的字段
 * @param int $id 更新主键值
 * @return mixed
 */
public function doUpdateById(array $attributes, int $id);
```

###### 根据主键删除id

```php
/**
 * 根据主键删除id
 * @param int $id
 * @return bool
 */
public function deleteById(int $id): bool;
```

###### 分页查询，不支持链表查询

```php
/**
 * 分页查询，不支持链表查询
 * @param int $limit 一页最多显示多少条，默认一页查询15条数据
 * @param array $columns 要查询的字段
 * @param array $condition 查询条件
 * @param array $orderBy 排序
 * @param null $page 页码
 * @param string $pageName
 * @return mixed
 */
public function paginate($limit = 15, array $columns = ['*'], array $condition = [], array $orderBy = [], $page = null, $pageName = 'page');
```

###### 根据主键，更新某个字段，模型要指定主键名

```php
/**
 * 根据主键，更新某个字段
 * @param int $id 主键id值
 * @param string $filedName 字段名称
 * @param string $fieldValue 字段值
 * @return mixed
 */
public function doUpdateFieldById(int $id, string $filedName, string $fieldValue);
```
###### 统计数量

```php
/**
 * 统计数量
 * @param array $condition
 * @param string $pkId
 * @return mixed
 */
public function count(array $condition = [], string $pkId = '');
```

###### 解析数据，返回数组
> 解析单条数据
```php
/**
 * 解析一条业务数据
 * @param array $row
 * @return array
 */
public function parseDataRow(array $row): array;
```
> 解析N条数据
```php
/**
 * 解析多条业务数据格式，循环调用 parseDataRow 方法，只需要在具体的业务逻辑继承重写 parseDataRow 方法即可
 * @param array $rows
 * @return array
 */
public function parseDataRows(array $rows): array;

```
#### AopCrypt 字符串可逆加密
使用的是支付宝支付包里面的加密
```php

use JoyceZ\LaravelLib\Aop\AopCrypt;

$value='your string';
// withScrectKey 设置加密密钥，默认为空字符串
(new AopCrypt())->withScrectKey(env('APP_KEY'))->decrypt($value);
//使用配置文件中加密密钥
(new AopCrypt())->withScrectKey()->decrypt($value);
//自定义加密密钥
(new AopCrypt())->withScrectKey(config('laraveladmin.crypt.screct_key'))->decrypt($value);
```

#### EncryptTableDbAttribute Eloquent 模型属性加密和解密

- 不支持模糊搜索，只支持精准搜索
- 使用 env('APP_KEY') 作为加解密key
- 依赖 `JoyceZ\LaravelLib\Aop\AopCrypt` 加密工具 

```php
use JoyceZ\LaravelLib\Traits\EncryptTableDbAttribute;

class Client extends Model {

    use EncryptTableDbAttribute;
   
    /**
     * 
     * @var array  需要加密解密的字段
     */
    protected $encryptTable = [
        'id_number', 
        'email',
    ];
}
```

#### 图形验证码

```php

use JoyceZ\LaravelLib\Helpers\ResultHelper;
use JoyceZ\LaravelLib\Contracts\Captcha as CaptchaInterface;

class Passport extends Controller {

        /**
         * 获取图形验证码
         * @param CaptchaInterface $captchaRepo
         * @return array
         */
        public function captcha(CaptchaInterface $captchaRepo)
        {
            $captcha = $captchaRepo->makeCode()->get();
            $captchaImg = Arr::get($captcha, 'image', '');
            $captchaUniqid = Arr::get($captcha, 'uniq', '');
            return ResultHelper::returnFormat('success', ResponseCode::SUCCESS, [
                'captcha' => $captchaImg,
                config('laraveladmin.passport.check_captcha_cache_key') => $captchaUniqid
            ]);
        }
}
```

#### 密码验证



```php

use JoyceZ\LaravelLib\Helpers\ResultHelper;
use JoyceZ\LaravelLib\Aop\AopPassword;
use App\Http\ResponseCode;

class Passport extends Controller {

        /**
         * 登录
         * @param Request $request
         * @return array
         */
        public function login(Request $request)
        {
           $params = $request->all();
           $user=User::where('username',$params['username'])->find();
           $pwdFlag = (new AopPassword())
                    ->withSalt()
                    ->check($user['password'], $params['password'], $user['pwd_salt']);
                if (!$pwdFlag) {
                    return ResultHelper::returnFormat('账号密码错误', ResponseCode::ERROR);
                }
                //密码加密
                //$salt = Str::random(6);
                //(new AopPassword())->withSalt(config('laraveladmin.passport.password_salt'))->encrypt('123456', $salt)

        }
}
```


