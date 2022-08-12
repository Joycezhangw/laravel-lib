<?php


namespace JoyceZ\LaravelLib\Repositories\Interfaces;


use JoyceZ\LaravelLib\Contracts\RepoCriteria;

/**
 * Interface CriteriaInterface
 * @package JoyceZ\LaravelLib\Repositories\Interfaces
 */
interface CriteriaInterface
{
    /**
     * @param bool $status
     * @return mixed
     */
    public function skipCriteria($status = true);

    /**
     * @return mixed
     */
    public function getCriteria();

    /**
     * @param RepoCriteria $criteria
     * @return mixed
     */
    public function getByCriteria(RepoCriteria $criteria);

    /**
     * @param RepoCriteria $criteria
     * @return mixed
     */
    public function pushCriteria(RepoCriteria $criteria);

    /**
     * @return mixed
     */
    public function applyCriteria();

    /**
     * 重置 all Criterias
     * @return mixed
     */
    public function resetCriteria();
}
