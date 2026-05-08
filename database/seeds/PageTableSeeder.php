<?php

use Illuminate\Database\Seeder;

class PageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'pages'=>[
                [
                    'title' => 'About Us',
                    'slug' => 'about-us',
                    'page_type' => 'predefine-link',
                    'link' => 'about-us',
                    'status' => '1',
                ],
                [
                    'title' => 'Blog',
                    'slug' => 'blog',
                    'page_type' => 'predefine-link',
                    'link' => 'blog',
                    'status' => '1',
                ],
                [
                    'title' => 'Category',
                    'slug' => 'category',
                    'page_type' => 'predefine-link',
                    'link' => 'category',
                    'status' => '1',
                ],
                [
                    'title' => 'Contact Us',
                    'slug' => 'contact-us',
                    'page_type' => 'predefine-link',
                    'link' => 'contact-us',
                    'status' => '1',
                ],
                [
                    'title' => 'Download',
                    'slug' => 'downloads',
                    'page_type' => 'predefine-link',
                    'link' => 'download',
                    'status' => '1',
                ],
                [
                    'title' => 'Events',
                    'slug' => 'events',
                    'page_type' => 'predefine-link',
                    'link' => 'events',
                    'status' => '1',
                ],
                [
                    'title' => 'Services',
                    'slug' => 'services',
                    'page_type' => 'predefine-link',
                    'link' => 'services',
                    'status' => '1',
                ],
                [
                    'title' => 'FAQ',
                    'slug' => 'faqs',
                    'page_type' => 'predefine-link',
                    'link' => 'faqs',
                    'status' => '1',
                ],
                [
                    'title' => 'Gallery',
                    'slug' => 'gallery',
                    'page_type' => 'predefine-link',
                    'link' => 'gallery',
                    'status' => '1',
                ],
                [
                    'title' => 'Home',
                    'slug' => '',
                    'page_type' => 'predefine-link',
                    'link' => '',
                    'status' => '1',
                ],
                [
                    'title' => 'Notice',
                    'slug' => 'public-notice',
                    'page_type' => 'predefine-link',
                    'link' => 'notice',
                    'status' => '1',
                ],
                [
                    'title' => 'Online Registration',
                    'slug' => 'online-registration',
                    'page_type' => 'predefine-link',
                    'link' => 'online-registration',
                    'status' => '1',
                ],
                [
                    'title' => 'Pricing',
                    'slug' => 'pricing',
                    'page_type' => 'predefine-link',
                    'link' => 'pricing',
                    'status' => '1',
                ],
                [
                    'title' => 'Staff',
                    'slug' => 'staffs',
                    'page_type' => 'predefine-link',
                    'link' => 'staff',
                    'status' => '1',
                ],
                [
                    'title' => 'Certificate Verification',
                    'slug' => 'certificate-verification',
                    'page_type' => 'predefine-link',
                    'link' => 'certificate-verification',
                    'status' => '0',
                ],
            ],
        ];

        foreach( $data['pages'] as $page){
            DB::table('web_pages')->insert([
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
                'created_by' => 1,
                'title' => $page['title'],
                'slug' => $page['slug'],
                'page_type' => $page['page_type'],
                'link' => $page['link'],
                'status' => $page['status'],
            ]);
        }

    }
}
