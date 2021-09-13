<?php


namespace holoyan\EloquentFilter\Rules;


class OrderRule extends FilterRule
{
    /**
     *
     */
    private const DEFAULT_ORDERING = 'asc';

    /**
     * @param string $filterKey
     * @param $filterValue
     */
    public function handle(string $filterKey, $filterValue): void
    {
        $this->builder->orderBy($this->getColumn($filterKey), $this->getValue($filterValue) ?? self::DEFAULT_ORDERING);
    }
}