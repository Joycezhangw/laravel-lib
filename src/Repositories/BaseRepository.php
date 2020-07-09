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

namespace JoyceZ\LaravelLib\Repositories;

use Illuminate\Database\Eloquent\Model;
use JoyceZ\LaravelLib\Repositories\Interfaces\BaseInterface;

/**
 * Repositories 设计模式，实现逻辑容器仓库接口
 * Class BaseRepository
 * @package JoyceZ\LaravelLib\Repositories
 */
abstract class BaseRepository implements BaseInterface
{
    /**
     * 当前仓库模型
     * @var Model
     */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * 根据主键查询一条数据
     * @param int $id 主键id
     * @return mixed
     */
    public function getByPkId(int $id)
    {
        return $this->model->find($id);
    }

    /**
     * 根据条件，获取一条指定字段数据
     * @param array $condition 查询条件
     * @param array $columns 查询指定字段
     * @return mixed
     */
    public function first(array $condition, array $columns = ['*'])
    {
        return $this->model->where($condition)->first($columns);
    }

    /**
     * 查询不到抛出异常
     * @param array $condition
     * @return mixed
     */
    public function firstOrFail(array $condition)
    {
        return $this->model->where($condition)->firstOrFail();
    }

    /**
     * 根据条件，获取全部数据
     * @param array $condition 查询条件
     * @param array $columns 要查询的字段
     * @param string $orderBy 排序字段名
     * @param string $sortBy 排序方式
     * @return mixed
     */
    public function all(array $condition = ['1' => 1], $columns = ['*'], string $orderBy = '', string $sortBy = 'asc')
    {
        $orderBy = $orderBy===''  ? $this->model->getKeyName():$orderBy;
        return $this->model->where($condition)->orderBy($orderBy, $sortBy)->get($columns);
    }

    /**
     * 保存新模型，此方法会返回模型实例,需要在模型上指定 fillable 或 guarded 属性
     * @param array $attributes 需要保存的字段和值
     * @return mixed
     */
    public function doCreate(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * 根据主键id，更新一条数据
     * @param array $attributes 要更新的字段
     * @param int $id 更新主键值
     * @return mixed
     */
    public function doUpdateByPkId(array $attributes, int $id)
    {
        $pkId = $this->model->getKeyName() ?? 'id';
        return $this->model->where($pkId, $id)->update($attributes);
    }

    /**
     * 根据指定条件更新数据，批量更新
     * @param array $condition 更新条件
     * @param array $attributes 要更新的字段
     * @return mixed
     */
    public function doUpdateByCondition(array $condition, array $attributes)
    {
        return $this->model->where($condition)->update($attributes);
    }

    /**
     * 根据主键，更新某个字段，模型要指定主键名
     * @param int $id 主键id值
     * @param string $filedName 字段名称
     * @param string $fieldValue 字段值
     * @return mixed
     */
    public function doUpdateFieldByPkId(int $id, string $filedName, string $fieldValue)
    {
        $pkId = $this->model->getKeyName() ?? 'id';
        return $this->model->where($pkId, $id)->update([$filedName => $fieldValue]);
    }

    /**
     * 根据主键删除id，物理删除
     * @param int $id 主键id值
     * @return bool
     */
    public function deleteByPkId(int $id): bool
    {
        $pkId = $this->model->getKeyName() ?? 'id';
        return $this->model->where($pkId, $id)->delete();
    }

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
    public function count(array $condition = [], string $columns = ''): int
    {
        $pkField = $columns == '' ? $this->model->getKeyName() : $columns;
        return $this->model->where($condition)->count($pkField);
    }



}
