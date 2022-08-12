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

use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use JoyceZ\LaravelLib\Contracts\RepoCriteria;
use JoyceZ\LaravelLib\Exceptions\RepositoryException;
use JoyceZ\LaravelLib\Repositories\Interfaces\BaseInterface;
use JoyceZ\LaravelLib\Repositories\Interfaces\CriteriaInterface;
use Closure;

/**
 * Repositories 设计模式，实现逻辑容器仓库接口
 * Class BaseRepository
 * @package JoyceZ\LaravelLib\Repositories
 */
abstract class BaseRepository implements BaseInterface
{
    /**
     * @var Application
     */
    protected $app;
    /**
     * 当前仓库模型
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     * @param Application $app
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->makeModel();
        $this->boot();
    }

    public function boot()
    {
    }

    /**
     * 指定模型
     * @return mixed
     */
    abstract public function model();

    /**
     * 获取当前的model对象
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * make 模型
     * @return Model|mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function makeModel()
    {
        $model = $this->app->make($this->model());
        if (!$model instanceof Model) {
            throw new RepositoryException("Class {$this->model()} must be in instance of Illuminate\\Database\\Eloquent\\Model");
        }
        return $this->model = $model;
    }

    /**
     * 重置模型
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function resetModel()
    {
        $this->makeModel();
    }


    /**
     * 根据主键查询
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        $model = $this->model->find($id, $columns);
        $this->resetModel();
        return $model;
    }

    /**
     * 按字段值查询单条数据
     * @param $field
     * @param null $value
     * @param array $columns
     * @return mixed
     */
    public function findByField($field, $value = null, $columns = ['*'])
    {
        $model = $this->model->where($field, '=', $value)->first($columns);
        $this->resetModel();
        return $model;
    }

    /**
     * 按字段值查询列表
     * @param $field
     * @param null $value
     * @param array $columns
     * @return mixed
     */
    public function findAllByField($field, $value = null, $columns = ['*'])
    {
        $model = $this->model->where($field, '=', $value)->get($columns);
        $this->resetModel();
        return $model;
    }


    /**
     * 根据条件查询数据
     * @param array $where
     * @param array $columns
     * @return mixed
     * @throws RepositoryException
     */
    public function findWhere(array $where, $columns = ['*'])
    {
        $this->applyConditions($where);
        $model = $this->model->get($columns);
        $this->resetModel();
        return $model;
    }

    /**
     * 根据字段多个值获取数据列表
     * @param string $field
     * @param array $values
     * @param array $columns
     * @return mixed|void
     */
    public function findWhereIn(string $field, array $values, $columns = ['*'])
    {
        $model = $this->model->whereIn($field, $values)->get($columns);
        $this->resetModel();
        return $model;
    }

    /**
     * 查询不在指定字段值中的数据
     * @param string $field
     * @param array $values
     * @param array $columns
     * @return mixed|void
     */
    public function findWhereNotIn(string $field, array $values, $columns = ['*'])
    {
        $model = $this->model->whereNotIn($field, $values)->get($columns);
        $this->resetModel();
        return $model;
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
    public function first(array $columns = ['*'])
    {
        $results = $this->model->first($columns);
        $this->resetModel();
        return $results;
    }

    /**
     * 查找第一条数据，获取创建
     * @param array $attributes
     * @return mixed
     */
    public function firstOrCreate(array $attributes = [])
    {
        $model = $this->model->firstOrCreate($attributes);
        $this->makeModel();
        return $model;
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
     * 查询数据列表
     * @param array $columns
     * @return mixed
     */
    public function all(array $columns = ['*'])
    {
        $results = $this->model->get($columns);
        $this->resetModel();
        return $results;
    }

    /**
     * 查询条件
     * @param $where
     * @return $this|BaseInterface
     */
    public function where($where)
    {
        $this->model = $this->model->where($where);
        return $this;
    }

    /**
     * 设置查询数量
     * @param int $limit
     * @return $this|mixed
     */
    public function limit(int $limit)
    {
        $this->model = $this->model->limit($limit);
        return $this;
    }

    /**
     * 要查询的字段
     * @param array[] $values
     * @return $this|mixed
     */
    public function select($values)
    {
        $this->model = $this->model->select($values);
        return $this;
    }


    /**
     * 排序
     * @param $column
     * @param string $direction
     * @return $this|mixed
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->model = $this->model->orderBy($column, $direction);
        return $this;
    }


    /**
     * 获取分页
     * @param array $columns
     * @param null $limit
     * @return mixed
     */
    public function paginate($columns = ['*'], $limit = 0)
    {
        $limit = $limit <= 0 ? config('landao.paginate.page_size') : $limit;
        $results = $this->model->paginate($limit, $columns);
        $this->resetModel();
        return $results;
    }


    /**
     * 保存新模型，此方法会返回模型实例,需要在模型上指定 fillable 或 guarded 属性
     * @param array $attributes 需要保存的字段和值
     * @return mixed
     */
    public function create(array $attributes)
    {
        $result = $this->model->create($attributes);
        $this->resetModel();
        return $result;
    }

    /**
     * 批量插入操作
     * @param array $attributes
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function createBatch(array $attributes)
    {
        $result = $this->model->insert($attributes);
        $this->resetModel();
        return $result;
    }


    /**
     * 根据主键id，更新一条数据
     * @param array $attributes 要更新的字段
     * @param int $id 更新主键值
     * @return mixed
     */
    public function updateById(array $attributes, int $id)
    {
        $model = $this->model->find($id);
        $result = $model->update($properties);
        $this->resetModel();
        return $result;
    }

    /**
     * 根据指定条件更新数据，批量更新
     * @param array $condition 更新条件
     * @param array $attributes 要更新的字段
     * @return mixed
     */
    public function updateByWhere(array $condition, array $attributes)
    {
        $this->applyConditions($condition);
        $result = $this->model->update($attributes);
        $this->resetModel();
        return $result;
    }

    /**
     * 根据主键，更新某个字段，模型要指定主键名
     * @param int $id 主键id值
     * @param string $filedName 字段名称
     * @param string $fieldValue 字段值
     * @return mixed
     */
    public function doUpdateFieldById(int $id, string $filedName, string $fieldValue)
    {
        $model = $this->model->find($id);
        $model->$filedName = $fieldValue;
        $result = $model->save();
        $this->resetModel();
        return $result;
    }

    /**
     * 更新或者创建
     * @param array $attributes
     * @param array $values
     * @return mixed
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        $model = $this->model->updateOrCreate($attributes, $values);
        $this->resetModel();
        return $model;
    }

    /**
     * 批量更新多条数据，默认更新主键为id，若不是，就以数组第一个主键为key进行更熟数据
     * @param array $multipleData 二维数据
     * @return bool
     */
    public function updateBatch(array $multipleData = []): bool
    {
        try {
            if (empty($multipleData)) {
                throw new \Exception('数据不能为空');
            }
            $tableName = $this->model->getTable();
            //获取第一个数组
            $firstRow = current($multipleData);
            //获取数组keys
            $updateColumn = array_keys($firstRow);
            //默认以 id 为条件更新，如果没有则以第一个字段为更新条件
            $pkField = isset($firstRow['id']) ? 'id' : current($updateColumn);
            unset($updateColumn[0]);
            //组装sql语句
            $updateSql = 'UPDATE ' . $tableName . ' SET ';
            $sets = [];
            $bindings = [];
            foreach ($updateColumn as $column) {
                $setSql = '`' . $column . '` = CASE ';
                foreach ($multipleData as $datum) {
                    $setSql .= 'WHEN `' . $pkField . '` = ? THEN ? ';
                    $bindings[] = $datum[$pkField];
                    $bindings[] = $datum[$column];
                }
                $setSql .= 'ELSE `' . $column . '` END ';
                $sets[] = $setSql;
            }
            $updateSql .= implode(', ', $sets);
            $whereIn = collect($multipleData)->pluck($pkField)->values()->all();
            $bindings = array_merge($bindings, $whereIn);
            $whereIn = rtrim(str_repeat('?,', count($whereIn)), ',');
            $updateSql = rtrim($updateSql, ', ') . ' WHERE `' . $pkField . '` IN (' . $whereIn . ')';
            //出啊如预处理sql语句和对应绑定数据
            return DB::update($updateSql, $bindings) > 0 ? true : false;
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * 根据主键删除id，物理删除。返回的是影响行数
     * @param int $id 主键值
     * @return int
     */
    public function deleteById(int $id)
    {
        $model = $this->find($id);
        $result = $model->delete();
        $this->resetModel();
        return $result;

    }

    /**
     * 根据id批量删除
     * @param array $ids
     * @return int|mixed
     */
    public function deleteByIds(array $ids)
    {
        return $this->model->destroy($ids);
    }


    /**
     * 根据条件，批量删除数据
     * @param array $condition
     * @return bool|mixed|null
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function deleteWhere(array $condition)
    {
        $this->applyConditions($condition);
        $deleted = $this->model->delete();
        $this->resetModel();
        return $deleted;
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
        if ($condition) {
            $this->applyConditions($condition);
        }
        $result = $this->model->count($pkField);
        $this->resetModel();
        return $result;
    }

    /**
     * 求和
     * @param array $condition
     * @param string $columns
     * @return mixed
     */
    public function sum(array $condition = [], string $columns = '')
    {
        $pkField = $columns == '' ? $this->model->getKeyName() : $columns;
        if ($condition) {
            $this->applyConditions($condition);
        }
        $result = $this->model->sum($pkField);
        $this->resetModel();
        return $result;
    }

    /**
     * 求平均值
     * @param array $condition
     * @param string $columns
     * @return mixed
     */
    public function avg(array $condition = [], string $columns = '')
    {
        $pkField = $columns == '' ? $this->model->getKeyName() : $columns;
        if ($condition) {
            $this->applyConditions($condition);
        }
        $result = $this->model->avg($pkField);
        $this->resetModel();
        return $result;
    }

    /**
     * 求最大值
     * @param array $condition
     * @param string $columns
     * @return mixed
     */
    public function max(array $condition = [], string $columns = '')
    {
        $pkField = $columns == '' ? $this->model->getKeyName() : $columns;
        if ($condition) {
            $this->applyConditions($condition);
        }
        $result = $this->model->max($pkField);
        $this->resetModel();
        return $result;
    }

    /**
     * 求最小值
     * @param array $condition
     * @param string $columns
     * @return mixed
     */
    public function min(array $condition = [], string $columns = '')
    {
        $pkField = $columns == '' ? $this->model->getKeyName() : $columns;
        if ($condition) {
            $this->applyConditions($condition);
        }
        $result = $this->model->min($pkField);
        $this->resetModel();
        return $result;
    }

    /**
     * 根据条件，指定某个字段值递增
     * @param array $condition 条件
     * @param string $filedName 指定字段名
     * @param int $amount 自增数量
     * @return mixed
     */
    public function increment(array $condition, string $filedName, int $amount = 1)
    {
        if ($condition) {
            $this->applyConditions($condition);
        }
        $result = $this->model->increment($filedName, $amount);
        $this->resetModel();
        return $result;
    }

    /**
     * 根据条件，指定某个字段值递减
     * @param array $condition 条件
     * @param string $filedName 指定字段名
     * @param int $amount 递减数量
     * @return mixed
     */
    public function decrement(array $condition, string $filedName, int $amount = 1)
    {
        if ($condition) {
            $this->applyConditions($condition);
        }
        $result = $this->model->decrement($filedName, $amount);
        $this->resetModel();
        return $result;
    }

    /**
     * 解析一条业务数据
     * @param array $row
     * @return array
     */
    public function parseDataRow(array $row): array
    {
        return $row;
    }

    /**
     * 解析多条业务数据格式，循环调用 parseDataRow 方法，只需要在具体的业务逻辑继承重写 parseDataRow 方法即可
     * @param array $rows
     * @return array
     */
    public function parseDataRows(array $rows): array
    {
        $list = [];
        foreach ($rows as $row) {
            $list[] = $this->parseDataRow($row);
        }
        return $list;
    }

    /**
     * 得到某个列的数组
     * @param string $column 字段名 多个字段用逗号分隔
     * @param array $condition 查询条件
     * @param string $key 索引
     * @return array
     */
    public function column(string $column, $condition = [], string $key = ''): array
    {
        $this->applyCriteria();
        if ($condition) {
            $this->applyConditions($condition);
        }
        $field = array_map('trim', explode(',', $column));
        $resultSet = $this->model->get($field)->toArray();
        if (empty($resultSet)) {
            $result = [];
        } elseif (('*' == $column || strpos($column, ',')) && $key) {
            $result = array_column($resultSet, null, $key);
        } else {
            if (empty($key)) {
                $key = null;
            }
            if (strpos($column, ',')) {
                $column = null;

            }
            $result = array_column($resultSet, $column, $key);
        }
        $this->resetModel();
        return $result;
    }


    /**
     * @param array $relations
     * @return $this
     */
    public function with(array $relations)
    {
        $this->model = $this->model->with($relations);
        return $this;
    }

    public function withCount(array $relations)
    {
        // TODO: Implement withCount() method.
    }


    /**
     * 同步关联
     * @param $id
     * @param $relation
     * @param $attributes
     * @param bool $detaching
     * @return mixed
     */
    public function sync($id, $relation, $attributes, $detaching = true)
    {
        return $this->find($id)->{$relation}()->sync($attributes, $detaching);
    }


    /**
     * 将 where 查询条件，追加到模型
     * @param array $where
     */
    protected function applyConditions(array $where)
    {
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                list($field, $condition, $val) = $value;
                $condition = preg_replace('/\s\s+/', ' ', trim($condition));

                $operator = explode(' ', $condition);
                if (count($operator) > 1) {
                    $condition = $operator[0];
                    $operator = $operator[1];
                } else $operator = null;
                switch (strtoupper($condition)) {
                    case 'IN':
                        if (!is_array($val)) throw new RepositoryException("Input {$val} mus be an array");
                        $this->model = $this->model->whereIn($field, $val);
                        break;
                    case 'NOTIN':
                        if (!is_array($val)) throw new RepositoryException("Input {$val} mus be an array");
                        $this->model = $this->model->whereNotIn($field, $val);
                        break;
                    case 'DATE':
                        if (!$operator) $operator = '=';
                        $this->model = $this->model->whereDate($field, $operator, $val);
                        break;
                    case 'DAY':
                        if (!$operator) $operator = '=';
                        $this->model = $this->model->whereDay($field, $operator, $val);
                        break;
                    case 'MONTH':
                        if (!$operator) $operator = '=';
                        $this->model = $this->model->whereMonth($field, $operator, $val);
                        break;
                    case 'YEAR':
                        if (!$operator) $operator = '=';
                        $this->model = $this->model->whereYear($field, $operator, $val);
                        break;
                    case 'EXISTS':
                        if (!($val instanceof Closure)) throw new RepositoryException("Input {$val} must be closure function");
                        $this->model = $this->model->whereExists($val);
                        break;
                    case 'HAS':
                        if (!($val instanceof Closure)) throw new RepositoryException("Input {$val} must be closure function");
                        $this->model = $this->model->whereHas($field, $val);
                        break;
                    case 'HASMORPH':
                        if (!($val instanceof Closure)) throw new RepositoryException("Input {$val} must be closure function");
                        $this->model = $this->model->whereHasMorph($field, $val);
                        break;
                    case 'DOESNTHAVE':
                        if (!($val instanceof Closure)) throw new RepositoryException("Input {$val} must be closure function");
                        $this->model = $this->model->whereDoesntHave($field, $val);
                        break;
                    case 'DOESNTHAVEMORPH':
                        if (!($val instanceof Closure)) throw new RepositoryException("Input {$val} must be closure function");
                        $this->model = $this->model->whereDoesntHaveMorph($field, $val);
                        break;
                    case 'BETWEEN':
                        if (!is_array($val)) throw new RepositoryException("Input {$val} mus be an array");
                        $this->model = $this->model->whereBetween($field, $val);
                        break;
                    case 'BETWEENCOLUMNS':
                        if (!is_array($val)) throw new RepositoryException("Input {$val} mus be an array");
                        $this->model = $this->model->whereBetweenColumns($field, $val);
                        break;
                    case 'NOTBETWEEN':
                        if (!is_array($val)) throw new RepositoryException("Input {$val} mus be an array");
                        $this->model = $this->model->whereNotBetween($field, $val);
                        break;
                    case 'NOTBETWEENCOLUMNS':
                        if (!is_array($val)) throw new RepositoryException("Input {$val} mus be an array");
                        $this->model = $this->model->whereNotBetweenColumns($field, $val);
                        break;
                    case 'RAW':
                        $this->model = $this->model->whereRaw($val);
                        break;
                    default:
                        $this->model = $this->model->where($field, $condition, $val);
                }
            } else {
                $this->model = $this->model->where($field, '=', $value);
            }
        }
    }

    /**
     *  执行事务
     * 传入匿名函数就是自动，不传入就是手动
     * @param callable|null $callable
     * @return mixed|void
     */
    public function transaction(callable $callable = null)
    {
        if (is_null($callback)) {
            DB::beginTransaction();
            return;
        }
        DB::transaction(call_user_func($callback));
    }

    /**
     * 事务回滚
     * @return mixed|void
     */
    public function rollBack()
    {
        DB::rollBack();
    }

    /**
     * 事务提交
     * @return mixed|void
     */
    public function commit()
    {
        DB::commit();
    }


    /**
     * 打印sql语句
     * @param Closure $callback
     * @param string $tableName
     * @return mixed|void
     * @throws \Exception
     */
    public function getQuerySql(Closure $callback, string $tableName = '')
    {
        try {
            DB::connection($tableName)->enableQueryLog();
            $callback($this);
            dump(DB::getQueryLog());
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

}
