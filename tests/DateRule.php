<?php

namespace holoyan\EloquentFilter\Tests;

use holoyan\EloquentFilter\Rules\FilterRule;

class DateRule extends FilterRule
{
    /**
     * @param string $filterKey
     * @param $filterValue
     */
    public function handle(string $filterKey, $filterValue): void
    {
        // do something cool
        $this->builder->whereBetween($this->getColumn($filterKey), $this->getValue($filterValue));
    }
}
