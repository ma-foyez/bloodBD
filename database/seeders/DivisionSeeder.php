<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/division.json');

        if (!File::exists($path)) {
            throw new \Exception("division.json file not found!");
        }

        $divisions = json_decode(File::get($path), true);

        $now = Carbon::now();

        $data = array_map(function ($item) use ($now) {
            return [
                'id' => (int) $item['id'],
                'name' => $item['name'],
                'bn_name' => $item['bn_name'],
                'lat' => $item['lat'],
                'lon' => $item['lon'],
                'url' => trim($item['url']),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $divisions);

        // Disable foreign key checks to avoid truncate issues if re-seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('divisions')->truncate();
        DB::table('divisions')->insert($data);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
