<?php

namespace holoyan\EloquentFilter\Rules;

use holoyan\EloquentFilter\MakeInstance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;

abstract class FilterRule
{
    use MakeInstance;

    /**
     * @var
     */
    protected $column;

    /**
     * @var
     */
    protected $value;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @param  string  $filterKey
     * @param $filterValue
     */
    abstract public function handle(string $filterKey, $filterValue): void;

    /**
     * @param  Builder  $builder
     * @return $this
     */
    public function setBuilder(Builder $builder)
    {
        $this->builder = $builder;
        return $this;
    }

    /**
     * @param string $filterKey
     * @return Expression | string
     */
    public function getColumn(string $filterKey)
    {
        return $this->column ?? $filterKey;
    }

    /**
     * @param  string  $column
     * @return $this
     */
    public function setColumn(string $column)
    {
        $this->column = $column;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        if (is_callable($value)) {
            $this->value = $value();
        } else {
            $this->value = $value;
        }

        return $this;
    }

    /**
     * @param $filterValue
     * @return mixed
     */
    public function getValue($filterValue)
    {
        return $this->value ?? $filterValue;
    }
}
