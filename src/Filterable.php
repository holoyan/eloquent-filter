<?php


namespace holoyan\EloquentFilter;

trait Filterable
{

    public function scopeFilter($query, array $requestFilter, $class = null)
    {
        $class = $class ?? $this->getFilterClass();

        return (new $class($query, $requestFilter))->handle()->getBuilder();
    }

    /**
     * @return string
     */
    private function getFilterClass(): string
    {
        return static::$filterClass ?? "App\\Http\\Filters\\" . class_basename(static::class) . "Filter";
    }
}
