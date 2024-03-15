<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $photo = fake()->image(
            storage_path('app/public/user-photo'),
            width: 70,
            height: 70,
            fullPath: false
        );
        $fileName = basename($photo);
        $imagePathFormat = 'user-photo/' . $fileName;

        User::factory(2)->create([
            'photo' => $imagePathFormat
        ]);
    }
}
