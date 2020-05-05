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
            ['payment_units'=>'Implementacion'],
            ['payment_units'=>'Proyecto'],
            ['payment_units'=>'Archivo'],
            ['payment_units'=>'Licitacion'],
            ['payment_units'=>'Descuento'],
            ['payment_units'=>'Clausula'],
  
        ]);
    }
}
