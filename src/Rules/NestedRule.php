<?php

namespace holoyan\EloquentFilter\Rules;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class NestedRule extends FilterRule
{
    /**
     * @var array
     */
    public $rules = [];

    /**
     * @param array $rules
     * @return $this
     */
    public function setRules(array $rules): self
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @param string $filterKey
     * @param $filterValue
     */
    public function handle(string $filterKey, $filterValue): void
    {
        foreach ($filterValue as $nestedKey => $nestedValue) {
            if ($rule = Arr::get($this->getRules(), $nestedKey)) {
                $rule->setBuilder($this->builder)
                    ->setColumn(
                    $this->getColumn($filterKey)
                )
                    ->handle($nestedKey, $nestedValue);
            }
        }
    }
}