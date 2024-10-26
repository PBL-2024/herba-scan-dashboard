<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'user_id' => User::inRandomOrder()->first()->id,
                'cover' => 'https://via.placeholder.com/640x480.png/00ff00?text=quia',
                'nama' => 'Daun Sirih',
                'deskripsi' => '',
                'manfaat' => '',
                'pengolahan' => '',
            ],
            [
                'user_id' => User::inRandomOrder()->first()->id,
                'cover' => 'https://via.placeholder.com/640x480.png/00ff00?text=quia',
                'nama' => 'Daun Jambu Biji',
                'deskripsi' => '',
                'manfaat' => '',
                'pengolahan' => '',
            ],
            [
                'user_id' => User::inRandomOrder()->first()->id,
                'cover' => 'https://via.placeholder.com/640x480.png/00ff00?text=quia',
                'nama' => 'Daun Jeruk',
                'deskripsi' => '',
                'manfaat' => '',
                'pengolahan' => '',
            ],
            [
                'user_id' => User::inRandomOrder()->first()->id,
                'cover' => 'https://via.placeholder.com/640x480.png/00ff00?text=quia',
                'nama' => 'Daun Kumis Kucing',
                'deskripsi' => '',
                'manfaat' => '',
                'pengolahan' => '',
            ],
            [
                'user_id' => User::inRandomOrder()->first()->id,
                'cover' => 'https://via.placeholder.com/640x480.png/00ff00?text=quia',
                'nama' => 'Daun Kunyit',
                'deskripsi' => '',
                'manfaat' => '',
                'pengolahan' => '',
            ],
            [
                'user_id' => User::inRandomOrder()->first()->id,
                'cover' => 'https://via.placeholder.com/640x480.png/00ff00?text=quia',
                'nama' => 'Daun Pandan',
                'deskripsi' => '',
                'manfaat' => '',
                'pengolahan' => '',
            ],
            [
                'user_id' => User::inRandomOrder()->first()->id,
                'cover' => 'https://via.placeholder.com/640x480.png/00ff00?text=quia',
                'nama' => 'Daun Pepaya',
                'deskripsi' => '',
                'manfaat' => '',
                'pengolahan' => '',
            ],
            [
                'user_id' => User::inRandomOrder()->first()->id,
                'cover' => 'https://via.placeholder.com/640x480.png/00ff00?text=quia',
                'nama' => 'Daun Sirsak',
                'deskripsi' => '',
                'manfaat' => '',
                'pengolahan' => '',
            ],
            [
                'user_id' => User::inRandomOrder()->first()->id,
                'cover' => 'https://via.placeholder.com/640x480.png/00ff00?text=quia',
                'nama' => 'Daun Nangka',
                'deskripsi' => '',
                'manfaat' => '',
                'pengolahan' => '',
            ],
            [
                'user_id' => User::inRandomOrder()->first()->id,
                'cover' => 'https://via.placeholder.com/640x480.png/00ff00?text=quia',
                'nama' => 'Lidah Buaya',
                'deskripsi' => '',
                'manfaat' => '',
                'pengolahan' => '',
            ],
        ];

        foreach ($data as $plant) {
            \App\Models\Plant::create($plant);
        }
    }
}
