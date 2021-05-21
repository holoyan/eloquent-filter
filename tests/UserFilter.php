<?php

namespace holoyan\EloquentFilter\Tests;

use holoyan\EloquentFilter\Filter;
use holoyan\EloquentFilter\Rules\SimpleRule;

class UserFilter extends Filter
{

    public function rules(): array
    {
        return [
            'name' => SimpleRule::make(),
            'email' => SimpleRule::make()->setComparisonType(SimpleRule::LIKE_COMPARISON_TYPES['both'])
        ];
    }
}
