<?php

use Illuminate\Database\Seeder;

class MenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'menus'=>[
                [
                    'title' => 'WELCOME PAGE',
                    'slug' => 'welcome_menu',
                    'rank' => '1',
                    'status' => '1',
                ],
                [
                    'title' => 'TOP NAV',
                    'slug' => 'top_menu',
                    'rank' => '10',
                    'status' => '1',
                ],
                [
                    'title' => 'MAIN NAVIGATION',
                    'slug' => 'main_navigation_menu',
                    'rank' => '20',
                    'status' => '1',
                ],
                [
                    'title' => 'STICKY NAVIGATION',
                    'slug' => 'sticky_navigation_menu',
                    'rank' => '30',
                    'status' => '1',
                ],
                [
                    'title' => 'USEFUL LINKS',
                    'slug' => 'useful_links',
                    'rank' => '40',
                    'status' => '1',
                ],
                [
                    'title' => 'FOOTER',
                    'slug' => 'footer_menu',
                    'rank' => '50',
                    'status' => '1',
                ],
            ],
        ];

        foreach( $data['menus'] as $section){
            DB::table('web_menus')->insert([
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
                'created_by' => 1,
                'title' => $section['title'],
                'slug' => $section['slug'],
                'rank' => $section['rank'],
                'status' => $section['status'],
            ]);
        }

    }
}
