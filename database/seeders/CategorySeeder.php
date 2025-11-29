<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Penting'],
            ['name' => 'Undangan'],
            ['name' => 'Internal'],
            ['name' => 'Permohonan'],
            ['name' => 'Pemberitahuan'],
            ['name' => 'Laporan'],
            ['name' => 'Surat Tugas'],
            ['name' => 'Surat Keputusan'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}