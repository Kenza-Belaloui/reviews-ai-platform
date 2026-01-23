<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@reviews.local'],
            ['name' => 'Admin', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        // Demo user (pratique pour le prof)
        $demoUser = User::updateOrCreate(
            ['email' => 'kenza2@test.com'],
            ['name' => 'Kenza User', 'password' => Hash::make('password123'), 'role' => 'user']
        );

        // Autres users
        User::factory()->count(4)->create(['role' => 'user']);

        // Reviews: beaucoup pour remplir dashboard
        $users = User::where('role', 'user')->get();

        foreach (range(1, 120) as $i) {
            Review::factory()->create([
                'user_id' => $users->random()->id,
            ]);
        }

        // Optionnel : ajouter quelques reviews “riches” au demo user
        Review::factory()->count(8)->create(['user_id' => $demoUser->id]);
    }
}
