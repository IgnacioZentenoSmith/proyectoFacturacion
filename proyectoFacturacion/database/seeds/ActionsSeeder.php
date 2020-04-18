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
		['actionName'=>'Administracion', 'actionParentId'=>null, 'actionType'=>'Menu'],
		['actionName'=>'Clientes', 'actionParentId'=>null, 'actionType'=>'Menu'],
		['actionName'=>'Contratos', 'actionParentId'=>null, 'actionType'=>'Menu'],
        ['actionName'=>'Facturas', 'actionParentId'=>null, 'actionType'=>'Menu'],
        ['actionName'=>'Administracion_create', 'actionParentId'=>1, 'actionType'=>'Programa'],
        ['actionName'=>'Administracion_edit', 'actionParentId'=>1, 'actionType'=>'Programa'],
        ['actionName'=>'Administracion_editPermisos', 'actionParentId'=>1, 'actionType'=>'Programa'],
        ['actionName'=>'Administracion_delete', 'actionParentId'=>1, 'actionType'=>'Programa'],   
	]);
    }
}
