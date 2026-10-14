<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class AvenzaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
{
    if (! DB::table('users')->where('email', 'admin@avenza.test')->exists()) {
        DB::table('users')->insert([
            'name'       => 'Avenza Admin',
            'email'      => 'admin@avenza.test',
            'password'   => Hash::make('admin12345'),
            'role'       => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    $categories = [
        ['Living Room', 'living-room', 'Sofas, coffee tables, TV racks'],
        ['Bedroom',     'bedroom',     'Beds, wardrobes, nightstands'],
        ['Dining',      'dining',      'Dining sets, chairs, buffets'],
        ['Office',      'office',      'Desks, office chairs, shelves'],
    ];

    foreach ($categories as [$name, $slug, $desc]) {
        if (! DB::table('categories')->where('slug', $slug)->exists()) {
            DB::table('categories')->insert([
                'category_name' => $name,
                'slug'          => $slug,
                'description'   => $desc,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}
}
