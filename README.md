# 常用的一些扩展类库

## 依赖

> PHP >=7.1
> Composer
> Laravel 5.5 +

## 命名空间

> `\\JoyceZ\\LaravelLib\\`

## 安装

`composer require joycezhang/laravellib`

## 用法

### Repositories 逻辑容器仓库设计模式

需要在服务容器中绑定`Repositories`

` $this->app->bind(IManage::class, ManageRepo::class);//管理员 `

建议新建一个服务类 `RepositoryServiceProvider` 用于专门管理`Repositories`服务的绑定

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

```
/**
 * 根据主键id获取单条数据
 * @param int $id 主键id
 * @return mixed
 */
public function getByPkId(int $id);

```

###### 根据条件，获取一条指定字段数据

```
/**
 * 根据条件，获取一条指定字段数据
 * @param array $columns  要查询字段
 * @param array $condition 查询条件
 * @return mixed
 */
public function first(array $condition, array $columns = ['*']);
```

###### 没有查找到单条数据，抛出异常

```
/**
* 没有查找到数据，抛出异常
* @param array $condition 查询条件
* @return mixed
*/
public function findOneOrFail(array $condition);
```

###### 获取全部数据

```
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
```
/**
 * 创建一条数据，不联表状态
 * @param array $attributes
 * @return mixed
 */
public function doCreate(array $attributes);
```
###### 根据主键id，更新一条数据
```
/**
 * 根据主键id，更新一条数据,模型要设定指定主键id
 * @param array $attributes 要更新的字段
 * @param int $id 更新主键值
 * @return mixed
 */
public function doUpdateById(array $attributes, int $id);
```

###### 根据主键删除id

```
/**
 * 根据主键删除id
 * @param int $id
 * @return bool
 */
public function deleteById(int $id): bool;
```

###### 分页查询，不支持链表查询

```
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

```
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

```
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


