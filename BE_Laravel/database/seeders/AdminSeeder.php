<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create();
        $role = Role::create([
            'role_name' => 'Admin',
        ]);
        UserRole::create([
            'user_id' => $admin->id,
            'role_id' => $role->id,
        ]);
    }
}
