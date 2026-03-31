<?php

namespace Database\Seeders;

use App\Models\SiteLanguage;
use Illuminate\Database\Seeder;

class SiteLanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            ['language_code' => 'en', 'language_name' => 'English'],
            ['language_code' => 'et', 'language_name' => 'Estonian'],
        ];

        foreach ($languages as $language) {
            SiteLanguage::firstOrCreate(
                ['language_code' => $language['language_code']],
                ['language_name' => $language['language_name']]
            );
        }
    }
}
