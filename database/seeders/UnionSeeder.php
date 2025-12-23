<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class UnionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('data/union.json');

        if (!File::exists($path)) {
            throw new \Exception("union.json file not found!");
        }

        $items = json_decode(File::get($path), true);
        $now = Carbon::now();

        $data = [];
        foreach ($items as $item) {
            $data[] = [
                'id' => (int) $item['id'],
                'area_id' => (int) $item['parent_id'],
                'name' => $item['name'],
                'bn_name' => $item['bn_name'],
                'url' => trim($item['url']),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('unions')->truncate();

        foreach (array_chunk($data, 500) as $chunk) {
            DB::table('unions')->insert($chunk);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
