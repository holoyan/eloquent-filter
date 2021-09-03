# Eloquent Filter

PHP >= 7.1


## Table of Content

 - [Introduction](#introduction)
 - [Installation](#installation)
 - [Basic Usage](#basic-usage)
   - [Dynamic filters](#dynamic-filter)
   - [Customize column](#customize-column)
   - [Customize value](#customize-value)
   - [Available filters](#available-filters)
- [Relation Filter](#relation-filter)
- [Nested Filter](#nested-filter)
 - [Extending filter](#extending-filter)
   - [Custom Rules](#custom-rules)
 - [Credits](#credits)
 - [License](#license) 
 
 
 ## Introduction 
 
   An easy way to add custom filters to your eloquent models. Powerful, flexible and fully dynamic
   
   ```php

    $users = User::filter($request->all())->get();

```
   
 
 ## Installation
    composer require holoyan/eloquent-filter
    
 ## Basic Usage
 
 Let's say we want to return a list of users filtered by multiple parameters.
 For example this is our request url:
 `/users?email=jo&categories[]=3&categories[]=4&role=admin`
 
 `$request->all()` will return:
 
 ```php
[
    'email'       => 'jo',
    'categories'  => [3, 4],
    'role' => 'admin'
]

```
 To filter by all those parameters we can simply write
 
   ```php
    
    namespace App\Http\Controllers;
    
    use Illuminate\Http\Request;
    use App\Models\User;
    
    class UserController extends Controller
    {
        public function index(Request $request)
        {    
            return User::filter($request->all())->get();
        }
    }

```

In our `User` model we must import `Filterable` trait

```php

namespace App\Models;

// import Filterable trait
use holoyan\EloquentFilter\Filterable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Filterable;
    
    // other stuff here
}

```

then create class called `UserFilter` in `App\Http\Filters` folder

```php
namespace App\Http\Filters;

use holoyan\EloquentFilter\Filter;
use holoyan\EloquentFilter\Rules\SimpleRule;

class UserFilter extends Filter
{

    public function rules()
    {
        return [
            'email' => SimpleRule::make()->startsWith(),
            'categories' => SimpleRule::make(),
            'role' => SimpleRule::make(),
        ];
    }
}
```

Query returns all users whose name starts with `jo`, belongs to category 3 or 4 and have `role` admin.

By default, package will look for filter class in `App\Http\Filters` folder with name `ModelFilter`, but if you want ypu can customize this behavior.

add `$filterClass` static property in model.

```php

namespace App\Models;

use holoyan\EloquentFilter\Filterable;
use Illuminate\Foundation\Auth\User as Authenticatable;

// your custom filter class
use App\Filters\MyFilter;

class User extends Authenticatable
{
    use Filterable;

    public static $filterClass = MyCustomFilter::class;
    // other stuff here
}

```

## Dynamic filter

Sometimes you may want to use a dynamic filter depending on conditions.
In this case you can pass second argument to filter method which is the filter class:

```php

$filter = $user->isAdmin() ? AdminFilter::class : BasicFilter::class;

User::filter($request->all(), $filter)->get();

```

## Customize column

You can also customize your column name

```php

    public function rules()
    {
        return [
            'firstName' => SimpleRule::make()->setColumn('first_name'),
        ];
    }

```

## Customize value

```php

    public function rules()
    {
        return [
            'firstName' => SimpleRule::make()->setvalue(function($value){
                return $value . 'test';
}           ),
        ];
    }

```

## Available filters

 - `SimpleRule::make() - by default, this will check for exact match`
```php

    public function rules()
    {
        return [
            'name' => SimpleRule::make()
        ];
    }

```  
if you want to use `like` comparison type you can use one of those methods:

```php

        return [
            // where name="value"
            'name' => SimpleRule::make(),
            // where name like "value%"
            'name' => SimpleRule::make()->startsWith(),
            // where name like "%value"
            'name' => SimpleRule::make()->endsWith(),
            // where name like "%value%"
            'name' => SimpleRule::make()->contains()
        ];

```

 - `RawRule::make()` - this allows you to specify callback function with your own logic

```php

        return [
            'name' => RawRule::make()->setCallback(function($query, $column, $value){
                $query->where('name', '<>', $value);
            })
        ];

```
 - `RelationRule::class` - for relation filter, check [bellow](#relation-filter) for more details
    
## Relation filter


Suppose `User` has `Product` relation, and we want to return all users which have product which name starts with 'cook'

```php

        return [
            'name' => SimpleRule::make(),
            'products' => RelationRule::make()->setRelation('products')->setRules([
                'name' => SimpleRule::make()->startsWith(),
            ]),
        ];

```

This allows you recursively pass any rules you want

## Nested filter

To make nested filter we need to use `NestedRule::class`
```php
        return [
            'b_date' => NestedRule::make()->setRules([
                'from' => SimpleRule::make()->setOperator('>='),
                'to' => SimpleRule::make()->setOperator('<='),
            ])
        ];

```

## Extending filter

You can create your custom rules with custom logic

## Custom Rules

For creating custom rules all you need is to create you class extend from `use holoyan\EloquentFilter\Rules\FilterRule` class and implement `handle`method

```php

namespace App\MyNamespace;

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
        $this->builder->whereDate($filterKey, $filterValue);
    }
}
```
Thats all, now you can use custom rule;

```php

        return [
            'name' => SimpleRule::make(),
            'created_at' => DateRule::make(),
        ];

```

## Credits

Inspired by [and-m-a](https://github.com/and-m-a)

## License

MIT


