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
        $user = User::first();
        
        Address::create([
            'zipcode' => "83025647",
            'street' => 'Rua Diomira Moro Zen',
            'number' => '222',
            'city' => 'São José dos Pinhais',
            'state' => 'Paraná',
            'country' => 'Brasil',
            'complement' => 'Casa',
            'user_id' => $user->id
        ]);
    }
}
