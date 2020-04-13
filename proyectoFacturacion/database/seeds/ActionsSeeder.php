<?php

use Illuminate\Database\Seeder;

class ActionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('actions')->insert([
		['actionName'=>'Administración'],
		['actionName'=>'Usuarios'],
		['actionName'=>'Clientes'],
		['actionName'=>'Contratos'],
		['actionName'=>'Facturas']
	]);
    }
}
