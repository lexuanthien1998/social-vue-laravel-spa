<?php

use Illuminate\Database\Seeder;

class MusicTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('music')->insert([
            ['title' => "Lần Cuối",
            'artists' => 'Karik',
            'song' => '1614851604.mpeg',
            'authors' =>  1
            ],
            ['title' => "Mới Đây Thôi Mà",
            'artists' => 'Lý Tuấn Kiệt',
            'song' => '1614852548.mpeg',
            'authors' =>  1
            ],
            ['title' => "Em Say Rồi",
            'artists' => 'Thương Võ, ACV',
            'song' => '1614907696.mpeg',
            'authors' =>  1
            ],
            ['title' => "Nàng Thơ...Trời Giấu Trời Mang Đi",
            'artists' => 'AMEE, Hoàng Dũng',
            'song' => '1614909224.mpeg',
            'authors' =>  1
            ],
            ['title' => "Xin Cô Đơn Đi",
            'artists' => 'K-ICM, APJ',
            'song' => '1614912982.mpeg',
            'authors' =>  1
            ]
        ]);
    }
}
