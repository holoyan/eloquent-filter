<?php

namespace holoyan\EloquentFilter\Rules;

class RawRule extends FilterRule
{
    /**
     * @var callable
     */
    private $callback;

    public function setCallback(callable $callback): self
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function handle(string $filterKey, $filterValue): void
    {
        $callback = $this->callback;

        $callback($this->builder, $this->getColumn($filterValue), $this->getValue($filterValue));
    }
}
