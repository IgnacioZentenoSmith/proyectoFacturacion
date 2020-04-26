<?php

use Illuminate\Database\Seeder;

class ModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert([
            ['moduleName'=>'Gestor Comercial Inmobiliario', 'moduleParentId'=>null],
            ['moduleName'=>'Postventa Inmobiliaria', 'moduleParentId'=>null],
            ['moduleName'=>'Gestión Documental', 'moduleParentId'=>null],
            ['moduleName'=>'Estudio de Titulo', 'moduleParentId'=>null],
            ['moduleName'=>'Cotizador Web', 'moduleParentId'=>1],
            ['moduleName'=>'Centralizador', 'moduleParentId'=>1],
            ['moduleName'=>'Gleads', 'moduleParentId'=>1],
            ['moduleName'=>'Entrega', 'moduleParentId'=>2],
            ['moduleName'=>'Acceso Web', 'moduleParentId'=>2],
        ]);
    }
}
