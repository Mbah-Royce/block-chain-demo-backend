<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $json = File::get("database/seeders/Roles.json");
        $roles = json_decode($json);
        foreach ($roles as $key => $role) {
            Role::create([
                'name' => $role,
            ]);
        }
//test
        $json = File::get("database/seeders/GovKey.json");
        $info = json_decode($json);
        $user = User::create([
            'name' => $info->name,
            'email' => $info->email,
            'public_key' => $info->publicKey,
            'private_key' => $info->privateKey,
            'role' => 'government'
        ]);
        $user->wallet()->create();
        $user->roles()->attach([1]);
    }
}
