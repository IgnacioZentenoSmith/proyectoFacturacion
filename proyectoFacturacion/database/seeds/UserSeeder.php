<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            ['name'=>'IgZen', 'email'=>'izenteno@planok.com', 'role'=>'Administrador', 'status'=>'Activo', 'email_verified_at'=>'2020-04-18 14:08:08', 'password'=>'$2y$10$ilOd1shK6GGaAVIaGcX1leyKEigElwbAweRL3LMP3jcS0CbfbnuSK'],  
        ]);
    }
}
