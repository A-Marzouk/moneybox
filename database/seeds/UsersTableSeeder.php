<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect([
            [
                'email' => 'admin@moneybox.com',
                'password' => 'administrator',
                'plainPassword' => 'administrator',
                'username' => 'admin',
                'name' => 'admin',
            ],
        ])->each(function ($item) {
            User::create($item)->assignRole('admin');
        });

        collect([
            [
                'user' => [
                    'email' => 'client@moneybox.com',
                    'password' => '123456789',
                    'plainPassword' => '123456789',
                    'username' => 'client',
                    'name' => 'client 1',
                ],
                'client' => [
                    'percentage' => 5,
                    'plan' => 500,
                    'currency' => 'UAH',
                ],
            ],
        ])->each(function ($item) {
            app(User::class)->createClient($item);
        });
    }
}
