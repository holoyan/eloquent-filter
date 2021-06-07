<?php

namespace holoyan\EloquentFilter\Rules;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class RelationRule extends FilterRule
{
    /**
     * @var array
     */
    public $types;

    /**
     * @var string
     */
    public $relation;

    /**
     * @var array
     */
    public $rules;

    /**
     * @inheritDoc
     */
    public function handle(string $filterKey, $filterValue): void
    {
        if ($this->isMorph()) {
            $this->builder->whereHasMorph($this->getRelation($filterKey), $this->getTypes(), function (Builder $subBuilder) use ($filterValue) {
                $this->handleRules($subBuilder, $filterValue);
            });
        } else {
            $this->builder->whereHas($this->getRelation($filterKey), function (Builder $subBuilder) use ($filterValue) {
                $this->handleRules($subBuilder, $filterValue);
            });
        }
    }

    /**
     * @param Builder $subBuilder
     * @param $filterValue
     */
    public function handleRules(Builder $subBuilder, $filterValue): void
    {
        foreach ($filterValue as $subFilterKey => $subFilterValue) {
            if ($rule = Arr::get($this->getRules(), $subFilterKey)) {
                $rule->setBuilder($subBuilder)->handle($subFilterKey, $subFilterValue);
            }
        }
    }

    /**
     * @param array $types
     *
     * @return RelationRule
     */
    public function setTypes(array $types): self
    {
        $this->types = $types;

        return $this;
    }

    /**
     * @param string $relation
     *
     * @return RelationRule
     */
    public function setRelation(string $relation): RelationRule
    {
        $this->relation = $relation;

        return $this;
    }

    /**
     * @param array $rules
     *
     * @return RelationRule
     */
    public function setRules(array $rules): RelationRule
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * @return bool
     */
    private function isMorph(): bool
    {
        return (bool) $this->types;
    }

    /**
     * @return array
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @param string $requestKey
     *
     * @return string
     */
    public function getRelation(string $requestKey): string
    {
        return $this->relation ?? $requestKey;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}
