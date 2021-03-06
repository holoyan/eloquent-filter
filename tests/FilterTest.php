<?php

namespace holoyan\EloquentFilter\Tests;

use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Support\Facades\DB;

class FilterTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function user_name_email_test()
    {
        $user = User::inRandomOrder()->first();
        $request = [
            'name'  => $user->name,
            'email' => $user->email,
        ];

        $filteredUser = User::filter($request, UserFilter::class)->get();
        $this->assertEquals(1, $filteredUser->count());
    }

    /**
     * @test
     */
    public function user_name_email_test_fails()
    {
        $user = User::inRandomOrder()->first();
        $request = [
            'name'  => $user->name.'test',
            'email' => $user->email,
        ];

        $filteredUser = User::filter($request, UserFilter::class)->get();
        $this->assertEquals(0, $filteredUser->count());
    }

    /**
     * @test
     */
    public function multiple_user_name_email_test()
    {
        $user = User::inRandomOrder()->first();

        User::create([
            'name'   => $user->name,
            'email'  => $user->email.'test',
            'b_date' => $user->b_date,
            'height' => $user->height,
        ]);

        $request = [
            'name'  => $user->name,
            'email' => $user->email.'test',
        ];

        $filteredUser = User::filter($request, UserFilter::class)->get();
        $this->assertEquals(1, $filteredUser->count());

        $filteredUser = User::filter(['name' => $user->name], UserFilter::class)->get();
        $this->assertEquals(2, $filteredUser->count());
    }

    /**
     * @test
     */
    public function raw_filter_test()
    {
        $user = User::inRandomOrder()->first();
        $actualCount = User::where('height', '>', $user->height)->count();

        $request = [
            'height' => $user->height,
        ];

        $filteredUser = User::filter($request, UserFilter::class)->get();
        $this->assertEquals($actualCount, $filteredUser->count());
    }

    /**
     * @test
     */
    public function relation_filter_rule()
    {
        $product = Product::inRandomOrder()->first();
        // create products with same name for different users

        foreach (User::inRandomOrder()->take(rand(2, 10))->get() as $user) {
            Product::create([
                'name'    => $product->name,
                'user_id' => $user->id,
            ]);
        }

        $actualCount = User::whereHas('products', function ($query) use ($product) {
            $query->where('name', $product->name);
        })->count();

        $request = [
            'products' => [
                'name' => $product->name,
            ],
        ];

        $filteredUser = User::filter($request, UserFilter::class)->get();
        $this->assertEquals($actualCount, $filteredUser->count());
    }

    /**
     * @test
     */
    public function custom_rule_test()
    {
        $user = User::inRandomOrder()->first();
        $date = Carbon::createFromDate($user->b_date);

        $between = [
            $date->subDay()->toDateTimeString(),
            $date->addDays(5)->toDateTimeString(),
        ];

        $outDate1 = [
            $date->subDays(10),
            $date->subDays(3),
        ];

        $outDate2 = [
            $date->addDays(10),
            $date->addDays(12),
        ];

        $request1 = [
            'email'  => $user->email,
            'b_date' => $between,
        ];

        $request2 = [
            'email'  => $user->email,
            'b_date' => $outDate1,
        ];

        $request3 = [
            'email'  => $user->email,
            'b_date' => $outDate1,
        ];

        $filteredUser = User::filter($request1, UserFilter::class)->get();
        $this->assertEquals(1, $filteredUser->count());

        $filteredUser = User::filter($request2, UserFilter::class)->get();
        $this->assertEquals(0, $filteredUser->count());

        $filteredUser = User::filter($request3, UserFilter::class)->get();
        $this->assertEquals(0, $filteredUser->count());
    }

    /**
     * @test
     */
    public function nested_rule_test()
    {
        $faker = Factory::create();
        $startDate = $faker->dateTime();
        $endDate = $faker->dateTime();

        $request = [
            'date'  => [
                'from' => $startDate,
                'to' => $endDate
            ],
        ];

        $filteredUser = User::filter($request, UserFilter::class)->get();
        $this->assertEquals(
            User::where('b_date', '<=', $endDate)->where('b_date', '>=', $startDate)->get()->count(),
            $filteredUser->count()
        );
    }

    /**
     * @test
     */
    public function order_by_id_desc_test()
    {
        $request = [
            'order' => 'desc'
        ];

        $latest = DB::table('users')->orderBy('id', 'desc')->first();
        $filteredUser = User::filter($request, UserFilter::class)->first();
        $this->assertEquals($latest->id, $filteredUser->id);
    }

    /**
     * @test
     */
    public function order_by_id_asc_test()
    {
        $request = [
            'order' => 'asc'
        ];

        $latest = DB::table('users')->orderBy('id', 'asc')->first();
        $filteredUser = User::filter($request, UserFilter::class)->first();
        $this->assertEquals($latest->id, $filteredUser->id);
    }

    /**
     * @test
     */
    public function order_by_b__date__desc_test()
    {
        $request = [
            'orderByDate' => 'desc'
        ];

        $latest = DB::table('users')->orderBy('b_date', 'desc')->first();
        $filteredUser = User::filter($request, UserFilter::class)->first();
        $this->assertEquals($latest->id, $filteredUser->id);
    }

    /**
     * @test
     */
    public function order_by_b__date__asc_test()
    {
        $request = [
            'orderByDate' => 'asc'
        ];

        $latest = DB::table('users')->orderBy('b_date', 'asc')->first();
        $filteredUser = User::filter($request, UserFilter::class)->first();
        $this->assertEquals($latest->id, $filteredUser->id);
    }
}
