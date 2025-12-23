<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/district.json');

        if (!File::exists($path)) {
            throw new \Exception("district.json file not found!");
        }

        $items = json_decode(File::get($path), true);

        $now = Carbon::now();

        $data = array_map(function ($item) use ($now) {
            return [
                'id' => (int) $item['id'],
                'division_id' => (int) $item['parent_id'],
                'name' => $item['name'],
                'bn_name' => $item['bn_name'],
                'lat' => $item['lat'],
                'lon' => $item['lon'],
                'url' => trim($item['url']),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $items);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('districts')->truncate();
        DB::table('districts')->insert($data);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
