<?php

namespace holoyan\EloquentFilter\Tests;

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
            'name' => $user->name,
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
            'name' => $user->name . 'test',
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
            'name' => $user->name,
            'email' => $user->email . 'test',
            'b_date' =>$user->b_date,
            'height' => $user->height
        ]);

        $request = [
            'name' => $user->name,
            'email' => $user->email . 'test',
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
            'height' => $user->height
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
                'name' => $product->name,
                'user_id' => $user->id
            ]);
        }

        $actualCount = User::whereHas('products', function($query) use ($product){
           $query->where('name', $product->name);
        })->count();

        $request = [
            'products' => [
                'name' => $product->name
            ]
        ];

        $filteredUser = User::filter($request, UserFilter::class)->get();
        $this->assertEquals($actualCount, $filteredUser->count());
    }

}
