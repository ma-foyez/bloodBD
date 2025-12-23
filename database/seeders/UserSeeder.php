<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->users() as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }

        $this->command->info('Users seeded successfully!');
    }

    private function users(): array
    {
        return [
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'superadmin@example.com',
                'username' => 'superadmin',
                'mobile' => '01700000000',
                'password' => Hash::make('12345678'),
                'dob' => '1990-01-01',
                'blood_group' => 'A+',
                'is_active' => true,
                'is_approved' => true,
                'is_weight_50kg' => true,
                'email_verified_at' => now(),
            ],
            [
                'first_name' => 'Muhammad',
                'last_name' => 'Abul Foyez',
                'email' => 'mafoyez.bd@gmail.com',
                'username' => 'mafoyez',
                'mobile' => '01871929132',
                'password' => Hash::make('12345678'),
                'dob' => '2001-05-05',
                'blood_group' => 'AB+',
                'is_active' => true,
                'is_approved' => true,
                'is_weight_50kg' => true,
                'email_verified_at' => now(),
            ],
        ];
    }
}
