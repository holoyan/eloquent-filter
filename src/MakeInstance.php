<?php

namespace holoyan\EloquentFilter;

trait MakeInstance
{
    /**
     * @param ...$options
     *
     * @return static
     */
    public static function make(...$options)
    {
        return new static(...$options);
    }
}
