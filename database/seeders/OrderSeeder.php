<?php

namespace Database\Seeders;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        if($users->isempty()){
            $users = User::factory()->count(10)->create();
        }
        foreach($users as $user){
            Order::factory()->count(1)->create([
                'user_id' => $user->user_id,
            ]);
        }
    }
}
