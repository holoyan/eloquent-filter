<?php

namespace holoyan\EloquentFilter;

use holoyan\EloquentFilter\Rules\FilterRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

abstract class Filter
{
    /**
     * Builder on which filter rules will be applied.
     *
     * @var Builder
     */
    private $builder;

    /**
     * Filter requests.
     *
     * @var array
     */
    private $filterRequests;

    /**
     * Filter rules, define how each filter request should be handled.
     *
     * @var FilterRule array
     */
    private $rules;

    public function __construct(Builder $builder, array $filterRequests)
    {
        $this->builder = $builder;
        $this->filterRequests = $filterRequests;
        $this->rules = $this->rules();
    }

    /**
     * Returns rules defined in derived class.
     *
     * @return array
     */
    abstract public function rules();

    /**
     * @return $this
     */
    public function handle(): self
    {
        foreach ($this->filterRequests as $requestKey => $requestValue) {
            if ($rule = Arr::get($this->rules, $requestKey)) {
                $rule->setBuilder($this->builder)->handle(
                    $rule->getColumn($requestKey),
                    $rule->getValue($requestValue)
                );
            }
        }

        return $this;
    }

    /**
     * @return Builder
     */
    public function getBuilder(): Builder
    {
        return $this->builder;
    }
}
