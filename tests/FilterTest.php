<?php

namespace holoyan\EloquentFilter\Tests;

use holoyan\EloquentFilter\Tests\User;

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
        $user = User::create([

        ]);
        $request = [
            'name' => $user->name . 'test',
            'email' => $user->email,
        ];

        $filteredUser = User::filter($request, UserFilter::class)->get();
        $this->assertEquals(0, $filteredUser->count());
    }

}
