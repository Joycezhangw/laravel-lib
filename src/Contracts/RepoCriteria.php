<?php


namespace JoyceZ\LaravelLib\Contracts;


use JoyceZ\LaravelLib\Generators\RepositoryGenerator;

/**
 * Interface CriteriaInterface
 * @package JoyceZ\LaravelLib\Contracts
 */
interface RepoCriteria
{
    public function apply($model, RepositoryGenerator $repository);
}