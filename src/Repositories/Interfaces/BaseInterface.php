<?php
// +----------------------------------------------------------------------
// | 通用类包
// +----------------------------------------------------------------------
// | Copyright (c) 2020 http://www.hmall.com.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: joyecZhang <zhangwei762@163.com>
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace JoyceZ\LaravelLib\Repositories\Interfaces;

/**
 * Repositories 设计模式，逻辑容器仓库接口
 * Interface BaseInterface
 * @package JoyceZ\LaravelLib\Repositories\Interfaces
 */
interface BaseInterface
{
    /**
     * 根据主键id获取单条数据
     * @param int $id 主键id
     * @return mixed
     */
    public function getByPkId(int $id);

    /**
     * 根据条件，获取一条指定字段数据
     * @param array $columns 查询字段
     * @param array $condition 查询条件
     * @return mixed
     */
    public function first(array $condition, array $columns = ['*']);


    /**
     * 没有查找到数据，抛出异常
     * @param array $condition
     * @return mixed
     */
    public function firstOrFail(array $condition);

    /**
     * 根据条件，获取全部数据
     * @param array $condition 查询条件
     * @param array $columns 要查询的字段
     * @param string $orderBy 排序字段名
     * @param string $sortBy 排序方式
     * @return mixed
     */
    public function all(array $condition = [], $columns = ['*'], string $orderBy = '', string $sortBy = 'asc');

    /**
     * 创建一条数据，不联表状态
     * @param array $attributes
     * @return mixed
     */
    public function doCreate(array $attributes);

    /**
     * 根据主键id，更新一条数据
     * @param array $attributes 要更新的字段
     * @param int $id 更新主键值
     * @return mixed
     */
    public function doUpdateByPkId(array $attributes, int $id);

    /**
     * 根据指定条件更新数据，批量更新
     * @param array $condition 更新条件
     * @param array $attributes 要更新的字段
     * @return mixed
     */
    public function doUpdateByCondition(array $condition, array $attributes);


    /**
     * 根据主键删除id，物理删除。返回的是影响行数
     * @param int $id 主键值
     * @return int
     */
    public function doDeleteByPkId(int $id): int;

    /**
     * 根据主键，更新某个字段，模型要指定主键名
     * @param int $id 主键id值
     * @param string $filedName 字段名称
     * @param string $fieldValue 字段值
     * @return mixed
     */
    public function doUpdateFieldByPkId(int $id, string $filedName, string $fieldValue);

    /**
     * 统计数量
     *
     * 注意：
     *     1.不建议使用 $columns='*'，请指定特定字段名，如果没指定，默认为主键字段名
     *     2.不建议用 count() 来判断数据存不存在，请使用find 或者 first 来判断数据是否存在
     *
     * @param array $condition 查询条件
     * @param string $columns 统计字段
     * @return int
     */
    public function count(array $condition = [], string $columns = ''): int;

    /**
     * 指定某个字段值自增
     * @param array $condition
     * @param string $filedName
     * @param int $amount
     * @return mixed
     */
    public function increment(array $condition, string $filedName, int $amount = 1);

    /**
     * 指定某个字段递减
     * @param array $condition
     * @param string $filedName
     * @param int $amount
     * @return mixed
     */
    public function decrement(array $condition, string $filedName, int $amount = 1);

    /**
     * 解析一条业务数据
     * @param array $row
     * @return array
     */
    public function parseDataRow(array $row): array;

    /**
     * 解析多条业务数据格式，循环调用 parseDataRow 方法，只需要在具体的业务逻辑继承重写 parseDataRow 方法即可
     * @param array $rows
     * @return array
     */
    public function parseDataRows(array $rows): array;
}
