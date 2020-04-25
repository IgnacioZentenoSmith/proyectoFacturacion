<?php

use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            ['idUser'=>'1', 'idActions'=>'1',], 
            ['idUser'=>'1', 'idActions'=>'2',], 
            ['idUser'=>'1', 'idActions'=>'3',], 
            ['idUser'=>'1', 'idActions'=>'4',], 
            ['idUser'=>'1', 'idActions'=>'5',], 
            ['idUser'=>'1', 'idActions'=>'6',], 
            ['idUser'=>'1', 'idActions'=>'7',], 
            ['idUser'=>'1', 'idActions'=>'8',],  
        ]);
    }
}
