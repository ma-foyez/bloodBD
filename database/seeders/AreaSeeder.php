<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/areas.json');

        if (!File::exists($path)) {
            throw new \Exception("areas.json file not found!");
        }

        $areas = json_decode(File::get($path), true);

        $now = Carbon::now();

        $data = array_map(function ($item) use ($now) {
            return [
                'id' => (int) $item['id'],
                'district_id' => (int) $item['parent_id'],
                'name' => $item['name'],
                'bn_name' => $item['bn_name'],
                'url' => trim($item['url']),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $areas);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('areas')->truncate(); // optional (fresh insert)
        DB::table('areas')->insert($data);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
