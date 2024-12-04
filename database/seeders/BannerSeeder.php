<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'image' => '1.png',
                'agent_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image' => '2.png',
                'agent_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image' => '3.png',
                'agent_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image' => '1.png',
                'agent_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image' => '2.png',
                'agent_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image' => '3.png',
                'agent_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image' => '1.png',
                'agent_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image' => '2.png',
                'agent_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image' => '3.png',
                'agent_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image' => '1.png',
                'agent_id' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image' => '2.png',
                'agent_id' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image' => '3.png',
                'agent_id' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('banners')->insert($banners);
    }
}
