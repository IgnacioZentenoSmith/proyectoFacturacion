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
        ['actionName'=>'Clientes_create', 'actionParentId'=>2, 'actionType'=>'Programa'], 
        ['actionName'=>'Clientes_edit', 'actionParentId'=>2, 'actionType'=>'Programa'],
        ['actionName'=>'Clientes_delete', 'actionParentId'=>2, 'actionType'=>'Programa'], 
        ['actionName'=>'Parametrizaciones', 'actionParentId'=>null, 'actionType'=>'Menu'],
        ['actionName'=>'Parametrizaciones_modulos', 'actionParentId'=>12, 'actionType'=>'Menu'],
        ['actionName'=>'Parametrizaciones_unidadesCobro', 'actionParentId'=>12, 'actionType'=>'Menu'], 
        ['actionName'=>'Parametrizaciones_modulos_create', 'actionParentId'=>13, 'actionType'=>'Programa'],
        ['actionName'=>'Parametrizaciones_modulos_edit', 'actionParentId'=>13, 'actionType'=>'Programa'],
        ['actionName'=>'Parametrizaciones_modulos_delete', 'actionParentId'=>13, 'actionType'=>'Programa'], 
        ['actionName'=>'Parametrizaciones_unidadesCobro_create', 'actionParentId'=>14, 'actionType'=>'Programa'],
        ['actionName'=>'Parametrizaciones_unidadesCobro_edit', 'actionParentId'=>14, 'actionType'=>'Programa'],
        ['actionName'=>'Parametrizaciones_unidadesCobro_delete', 'actionParentId'=>14, 'actionType'=>'Programa'],
	]);
    }
}
