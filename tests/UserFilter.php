<?php

namespace holoyan\EloquentFilter\Tests;

use holoyan\EloquentFilter\Filter;
use holoyan\EloquentFilter\Rules\NestedRule;
use holoyan\EloquentFilter\Rules\OrderRule;
use holoyan\EloquentFilter\Rules\RawRule;
use holoyan\EloquentFilter\Rules\RelationRule;
use holoyan\EloquentFilter\Rules\SimpleRule;

class UserFilter extends Filter
{
    public function rules(): array
    {
        return [
            'name'   => SimpleRule::make(),
            'email'  => SimpleRule::make()->setComparisonType(SimpleRule::LIKE_COMPARISON_TYPES['both']),
            'height' => RawRule::make()->setCallback(function ($builder, $column, $value) {
                $builder->where('height', '>', $value);
            }),
            'products' => RelationRule::make()->setRelation('products')->setRules([
                'name'    => SimpleRule::make(),
                'user_id' => SimpleRule::make(),
            ]),
            'b_date' => DateRule::make(),
            'date' => NestedRule::make()->setColumn('b_date')->setRules([
                'from' => SimpleRule::make()->setOperator('>='),
                'to' => SimpleRule::make()->setOperator('<='),
            ]),
            'order' => OrderRule::make()->setColumn('id'),
            'orderByDate' => OrderRule::make()->setColumn('b_date'),
        ];
    }
}
