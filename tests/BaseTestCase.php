<?php

namespace holoyan\EloquentFilter\Tests;

use Faker\Factory;
use holoyan\EloquentFilter\Filterable;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase;

class BaseTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->migrate();

        $this->seedModels();
    }

    private function migrate()
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('name');
            $table->dateTime('b_date');
            $table->integer('height')->comment('sm');
            $table->timestamps();
        });

        $this->app['db']->connection()->getSchemaBuilder()->create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('user_id');
            $table->timestamps();
        });
    }

    public function seedModels()
    {
        $faker = Factory::create();
        for ($i = 0; $i < 30; $i++) {
            $user = User::create([
                'name'   => $faker->name,
                'email'  => $faker->email,
                'b_date' => $faker->dateTime(),
                'height' => $faker->numberBetween(50, 200),
            ]);

            for ($j = 1; $j <= rand(1, 3); $j++) {
                Product::create([
                    'name'    => $faker->name,
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}

class User extends \Illuminate\Database\Eloquent\Model
{
    use Filterable;

    protected $fillable = ['email', 'name', 'b_date', 'height'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

class Product extends \Illuminate\Database\Eloquent\Model
{
    use Filterable;

    protected $fillable = ['name', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
