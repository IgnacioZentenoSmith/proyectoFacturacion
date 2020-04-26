<?php

use Illuminate\Database\Seeder;

class PaymentUnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_units')->insert([
            ['payment_unitName'=>'Implementacion'],
            ['payment_unitName'=>'Proyecto'],
            ['payment_unitName'=>'Archivo'],
            ['payment_unitName'=>'Licitacion'],
            ['payment_unitName'=>'Descuento'],
            ['payment_unitName'=>'Clausula'],
  
        ]);
    }
}
