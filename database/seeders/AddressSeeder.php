<?php

namespace Database\Seeders;
use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
    public function run(): void
    {
        $users =User::all();
        if($users->isempty()){
            $users = User::factory()->count(10)->create();
        }
        foreach($users as $user){
            Address::factory()->count(2)->create([
                'user_id'=> $user->user_id,
            ]);
        }
    }
}
