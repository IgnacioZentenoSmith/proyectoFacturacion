<?php

namespace App\Http\Controllers;

use App\Permission;
use App\ContractPaymentDetails;
use App\Client;
use App\Contracts;
use App\ContractConditions;
use App\PaymentUnits;
use App\Quantities;
use Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;



class ApiquantitiesController extends Controller
{

    public function apiQuantities() {
        $periodo = Carbon::now()->format('Y-m');

        $contracts = Contracts::all();
        foreach ($contracts as $contract) {
            //Saca todos los detalles del contrato de este periodo
            $contractPaymentDetails = ContractPaymentDetails::where('contractPaymentDetails_period', $periodo)
                ->where('idContract', $contract->id)
                ->get();

            //Hay al menos 1 detalle -> buscar condiciones contractuales
            if ($contractPaymentDetails->count() > 0) {
                if ($contract->contractsRecepcionMunicipal) {

                    $contractPaymentDetails = $contractPaymentDetails->unique(function ($item) {
                        return $item['contractPaymentDetails_glosaProyecto'].$item['contractPaymentDetails_recepcionMunicipal'];
                    });
                }
                //Sacar las condiciones contractuales validas para este periodo del contrato
                /*
                Donde sea en este contrato
                Donde la fecha de inicio sea menor o igual al 25 de este mes
                Donde la fecha de termino sea mayor o igual al 25 de este mes
                */
                $contractConditions = ContractConditions::where('idContract', $contract->id)
                ->where('contractsConditions_fechaInicio', '<=', $periodo . '-25')
                ->where(function ($query) use ($periodo) {
                    $query->whereNull('contractsConditions_fechaTermino')
                        ->orWhere('contractsConditions_fechaTermino', '>=', $periodo . '-25');
                })
                ->join('payment_units', 'payment_units.id', '=', 'contract_conditions.idPaymentUnit')
                ->join('modules', 'modules.id', '=', 'contract_conditions.idModule')
                ->select('contract_conditions.*', 'payment_units.payment_units', 'modules.moduleParentId')
                ->get();

                //Donde la modalidad sea fija o variable
                $contractConditions_FijoVariable = $contractConditions->whereIn('contractsConditions_Modalidad', ['Fijo', 'Variable']);

                //Hay al menos 1 -> generar cantidades
                if ($contractConditions_FijoVariable->count() > 0) {
                    foreach ($contractConditions_FijoVariable as $contractCondition_FijoVariable) {
                        //Modulo padre o el modulo es GCI / PVI
                        if ($contractCondition_FijoVariable->idModule == 1 || $contractCondition_FijoVariable->idModule == 2 ||
                            $contractCondition_FijoVariable->moduleParentId == 1 || $contractCondition_FijoVariable->moduleParentId == 2) {
                            $this->calculate_GCIPVIquantities($periodo, $contractCondition_FijoVariable, $contractPaymentDetails, $contractConditions);
                        }
                        //Modulo padre o el modulo es DTP / LICITA
                        else if ($contractCondition_FijoVariable->idModule == 3 || $contractCondition_FijoVariable->idModule == 12 ||
                                $contractCondition_FijoVariable->moduleParentId == 3 || $contractCondition_FijoVariable->moduleParentId == 12) {
                            $this->calculate_DTPLICITAquantities($periodo, $contractCondition_FijoVariable, $contractPaymentDetails, $contractConditions);
                        }
                    }
                }
            }
        }
    }


    private function calculate_DTPLICITAquantities($periodo, $contractCondition_FijoVariable, $contractPaymentDetails, $contractConditions) {
        $detalles = $contractPaymentDetails->where('idPaymentUnit', $contractCondition_FijoVariable->idPaymentUnit);
        if ($contractCondition_FijoVariable->contractsConditions_Modalidad == 'Fijo') {
            $quantityMonto = $contractCondition_FijoVariable->contractsConditions_Precio;
            //Si no existe, crear
            $checkQuantity = Quantities::where('idContractCondition', $contractCondition_FijoVariable->id)
                ->where('quantitiesCantidad', 1)
                ->where('quantitiesPeriodo', $periodo)
                ->where('quantitiesMonto', $quantityMonto)
                ->first();
            if ($checkQuantity == null) {
                $newQuantities = new Quantities([
                    'idContractCondition' => $contractCondition_FijoVariable->id,
                    'quantitiesCantidad' => 1,
                    'quantitiesPeriodo' => $periodo,
                    'quantitiesMonto' => $quantityMonto,
                ]);
                //Guardar la cantidad
                $newQuantities->save();
            }
        }
        if ($detalles->count() > 0) {

            foreach ($detalles as $detalle) {
                $cantidadDetalles = $detalle->ccontractPaymentDetails_quantity;

                if ($contractCondition_FijoVariable->contractsConditions_Modalidad == 'Variable') {
                    $variableConditions = $contractConditions->where('idPaymentUnit', $contractCondition_FijoVariable->idPaymentUnit)
                    ->where('contractsConditions_Modalidad', '!=', 'Fijo');
                    //Ordenar
                    $sortedVariableConditions = $this->sortVariableConditions($variableConditions);
                    //Sacar la cantidad maxima
                    $maxCantidad = $sortedVariableConditions->whereIn('contractsConditions_Modalidad', ['Variable', 'Escalonado'])
                    ->max('contractsConditions_Cantidad');
                    //Montos iterables
                    $quantityMonto = 0;
                    $escalonAnterior = 0;
                    //ID de la condicion "variable"
                    $variableConditionID = $sortedVariableConditions->where('contractsConditions_Modalidad', 'Variable')[0]->id;

                    foreach ($sortedVariableConditions as $sortedVariableCondition) {
                        //Variable
                        if ($sortedVariableCondition->contractsConditions_Modalidad == 'Variable') {
                            if ($cantidadDetalles > 0) {
                                $quantityMonto = $sortedVariableCondition->contractsConditions_Precio;
                            }
                            $escalonAnterior = $sortedVariableCondition->contractsConditions_Cantidad;
                        }
                        //Escalonado
                        else if ($sortedVariableCondition->contractsConditions_Modalidad == 'Escalonado') {
                            $cantidadCondicion = $sortedVariableCondition->contractsConditions_Cantidad;
                            //Si esta entre este escalon y el anterior
                            if ($cantidadDetalles > $escalonAnterior && $cantidadDetalles <= $cantidadCondicion) {
                                $quantityMonto = $sortedVariableCondition->contractsConditions_Precio;
                            }
                            //Si es mayor al escalon, pasar al siguiente
                            else if ($cantidadDetalles >= $cantidadCondicion) {
                                $quantityMonto = $sortedVariableCondition->contractsConditions_Precio;
                            }
                            $escalonAnterior = $sortedVariableCondition->contractsConditions_Cantidad;
                        }

                        //Adicional
                        else if ($sortedVariableCondition->contractsConditions_Modalidad == 'Adicional') {
                            //Si la cantidad de detalles menos el mayor escalon es mayor que 0
                            if ($cantidadDetalles - $maxCantidad > 0) {
                                $cantidadCondicion = $sortedVariableCondition->contractsConditions_Cantidad;
                                //Si la cantidad del adicional es mayor a 1, sacar division entera
                                if ($cantidadCondicion > 1) {
                                    //Redondea hacia arriba
                                    $quantityMonto += round(($cantidadDetalles - $maxCantidad) / $cantidadCondicion) * $sortedVariableCondition->contractsConditions_Precio;
                                } else if ($cantidadCondicion == 1) {
                                    $quantityMonto += ($cantidadDetalles - $maxCantidad) * $sortedVariableCondition->contractsConditions_Precio;
                                }
                            }
                        }
                        //Descuento
                        else if ($sortedVariableCondition->contractsConditions_Modalidad == 'Descuento') {
                            $quantityMonto = round($quantityMonto * (100 - $sortedVariableCondition->contractsConditions_Precio) / 100, 2);
                        }
                    }
                    $checkQuantity = Quantities::where('idContractCondition', $variableConditionID)
                    ->where('quantitiesCantidad', $cantidadDetalles)
                    ->where('quantitiesPeriodo', $periodo)
                    ->where('quantitiesMonto', $quantityMonto)
                    ->first();

                    if ($checkQuantity == null) {
                        $newQuantities = new Quantities([
                            'idContractCondition' => $variableConditionID,
                            'quantitiesCantidad' => $cantidadDetalles,
                            'quantitiesPeriodo' => $periodo,
                            'quantitiesMonto' => $quantityMonto,
                        ]);
                        //Guardar la cantidad
                        $newQuantities->save();
                    }
                }
            }
        }
    }

    private function calculate_GCIPVIquantities ($periodo, $contractCondition_FijoVariable, $contractPaymentDetails, $contractConditions) {
        //Sacar los detalles de esta unidad de pago
        $detalles = $contractPaymentDetails->where('idPaymentUnit', $contractCondition_FijoVariable->idPaymentUnit);

        //Unidades por proyecto
        if ($contractCondition_FijoVariable->payment_units == 'Unidades por proyecto') {
            $unidadesPorProyectoConditions = $contractConditions->where('payment_units', 'Unidades por proyecto');
            $this->calculate_UnidadesPorProyectoQuantities($unidadesPorProyectoConditions, $detalles, $periodo);
        }
        //Otras unidades de cobro

        else {
            $cantidadDetalles = $detalles->count();
            //Fijo
            if ($contractCondition_FijoVariable->contractsConditions_Modalidad == 'Fijo') {
                $quantityMonto = $contractCondition_FijoVariable->contractsConditions_Precio;

                $checkQuantity = Quantities::where('idContractCondition', $contractCondition_FijoVariable->id)
                ->where('quantitiesCantidad', 1)
                ->where('quantitiesPeriodo', $periodo)
                ->where('quantitiesMonto', $quantityMonto)
                ->first();

                if ($checkQuantity == null) {
                    $newQuantities = new Quantities([
                        'idContractCondition' => $contractCondition_FijoVariable->id,
                        'quantitiesCantidad' => 1,
                        'quantitiesPeriodo' => $periodo,
                        'quantitiesMonto' => $quantityMonto,
                    ]);
                    //Guardar la cantidad
                    $newQuantities->save();
                }
            }
            //Variable
            else if ($contractCondition_FijoVariable->contractsConditions_Modalidad == 'Variable')  {
                //Ordenar las condiciones -> variable / escalonados ... (de menor a mayor precio)  / adicional
                $variableConditions = $contractConditions->where('idPaymentUnit', $contractCondition_FijoVariable->idPaymentUnit);
                //Ordenar
                $sortedVariableConditions = $this->sortVariableConditions($variableConditions);
                //Sacar la cantidad maxima
                $maxCantidad = $sortedVariableConditions->whereIn('contractsConditions_Modalidad', ['Variable', 'Escalonado'])
                ->max('contractsConditions_Cantidad');

                $quantityMonto = 0;
                $escalonAnterior = 0;

                foreach ($sortedVariableConditions as $sortedVariableCondition) {
                    //Variable
                    if ($sortedVariableCondition->contractsConditions_Modalidad == 'Variable') {
                        $cantidadCondicion = $sortedVariableCondition->contractsConditions_Cantidad;
                        $escalonAnterior = $cantidadCondicion;

                        if ($cantidadDetalles > 0 && $cantidadDetalles <= $cantidadCondicion) {
                            $quantityMonto = $sortedVariableCondition->contractsConditions_Precio;
                        }
                        if ($cantidadDetalles >= $cantidadCondicion) {
                            $quantityMonto = $sortedVariableCondition->contractsConditions_Precio;
                        }
                    }
                    //Escalonado
                    else if ($sortedVariableCondition->contractsConditions_Modalidad == 'Escalonado') {
                        $cantidadCondicion = $sortedVariableCondition->contractsConditions_Cantidad;
                        //Si es mayor
                        if ($cantidadDetalles >= $cantidadCondicion) {
                            $quantityMonto = $sortedVariableCondition->contractsConditions_Precio;
                        }
                        //Si es mayor al anterior y menor a este escalon
                        else if ($cantidadDetalles > $escalonAnterior && $cantidadDetalles <= $cantidadCondicion) {
                            $quantityMonto = $sortedVariableCondition->contractsConditions_Precio;
                        }
                        $escalonAnterior = $cantidadCondicion;
                    }

                    //Adicional
                    else if ($sortedVariableCondition->contractsConditions_Modalidad == 'Adicional') {
                        //Si la cantidad de detalles menos el mayor escalon es mayor que 0
                        if ($cantidadDetalles - $maxCantidad > 0) {
                            $cantidadCondicion = $sortedVariableCondition->contractsConditions_Cantidad;
                            //Si la cantidad del adicional es mayor a 1, sacar division entera
                            if ($cantidadCondicion > 1) {
                                //Redondea hacia arriba
                                $quantityMonto += round(($cantidadDetalles - $maxCantidad) / $cantidadCondicion) * $sortedVariableCondition->contractsConditions_Precio;
                            } else if ($cantidadCondicion == 1) {
                                $quantityMonto += ($cantidadDetalles - $maxCantidad) * $sortedVariableCondition->contractsConditions_Precio;
                            }
                        }

                    }
                    //Descuento
                    else if ($sortedVariableCondition->contractsConditions_Modalidad == 'Descuento') {
                        $quantityMonto = round($quantityMonto * (100 - $sortedVariableCondition->contractsConditions_Precio) / 100, 2);
                    }
                }


                $checkQuantity = Quantities::where('idContractCondition', $contractCondition_FijoVariable->id)
                ->where('quantitiesCantidad', $cantidadDetalles)
                ->where('quantitiesPeriodo', $periodo)
                ->where('quantitiesMonto', $quantityMonto)
                ->first();

                if ($checkQuantity == null) {
                    $newQuantities = new Quantities([
                        'idContractCondition' => $contractCondition_FijoVariable->id,
                        'quantitiesCantidad' => $cantidadDetalles,
                        'quantitiesPeriodo' => $periodo,
                        'quantitiesMonto' => $quantityMonto,
                    ]);
                    //Guardar la cantidad
                    $newQuantities->save();
                }
            }
        }
    }

    public function calculate_UnidadesPorProyectoQuantities($unidadesPorProyectoConditions, $detalles, $periodo) {
        $sortedVariableConditions = $unidadesPorProyectoConditions->sortBy('contractsConditions_Precio');
        $variableCondition = $sortedVariableConditions->where('contractsConditions_Modalidad', 'Variable');
        //Montos iterables
        $quantityMonto = 0;
        $escalonAnterior = 0;
        //Montos totales
        $montoTotal = 0;
        $cantidadDetallesTotal = 0;

        foreach ($sortedVariableConditions as $sortedVariableCondition) {
            $cantidadCondicion = $sortedVariableCondition->contractsConditions_Cantidad;
            //Variable
            if ($sortedVariableCondition->contractsConditions_Modalidad == 'Variable') {
                $detallesVariable = $detalles->where('contractPaymentDetails_units', '<=', $cantidadCondicion);
                $cantidadDetalles = $detallesVariable->count();
                $escalonAnterior = $cantidadCondicion;
                //Si hay al menos 1 proyecto q cumpla con esta condicion, cobrar
                if ($cantidadDetalles > 0) {
                    $quantityMonto = $sortedVariableCondition->contractsConditions_Precio * $cantidadDetalles;
                }
                //De lo contrario, no cobrar
                else {
                    $quantityMonto = null;
                }
            }
            //Escalonado
            else if ($sortedVariableCondition->contractsConditions_Modalidad == 'Escalonado') {
                //Buscar donde esten entre este escalon
                $detallesVariable = $detalles->where('contractPaymentDetails_units', '<=', $cantidadCondicion)
                ->where('contractPaymentDetails_units', '>=', $escalonAnterior);
                $cantidadDetalles = $detallesVariable->count();

                //Si hay al menos 1 proyecto q cumpla con esta condicion, cobrar
                if ($cantidadDetalles > 0) {
                    $quantityMonto = $sortedVariableCondition->contractsConditions_Precio * $cantidadDetalles;
                }
                //De lo contrario, no cobrar
                else {
                    $quantityMonto = null;
                }
                $escalonAnterior = $cantidadCondicion;
            }
            if ($quantityMonto != null) {
                $montoTotal += $quantityMonto;
                $cantidadDetallesTotal += $cantidadDetalles;
            }
        }

        $checkQuantity = Quantities::where('idContractCondition', $variableCondition[0]->id)
        ->where('quantitiesCantidad', $cantidadDetallesTotal)
        ->where('quantitiesPeriodo', $periodo)
        ->where('quantitiesMonto', $montoTotal)
        ->first();

        if ($checkQuantity == null) {
            $newQuantities = new Quantities([
                'idContractCondition' => $variableCondition[0]->id,
                'quantitiesCantidad' => $cantidadDetallesTotal,
                'quantitiesPeriodo' => $periodo,
                'quantitiesMonto' => $montoTotal,
            ]);
            //Guardar la cantidad
            $newQuantities->save();
        }
    }

    private function sortVariableConditions($variableConditions) {
        if ($variableConditions->where('contractsConditions_Modalidad', 'Adicional')) {
            $adicional = $variableConditions->where('contractsConditions_Modalidad', 'Adicional');
            $variableConditions = $variableConditions->where('contractsConditions_Modalidad', '!=', 'Adicional');
            $sortedVariableConditions = $variableConditions->sortBy('contractsConditions_Precio');
            $sortedVariableConditions = $sortedVariableConditions->concat($adicional);
        }
        else {
            $sortedVariableConditions = $variableConditions->sortBy('contractsConditions_Precio');
        }
        //Descuentos
        if ($variableConditions->where('contractsConditions_Modalidad', 'Descuento')) {
            $descuento = $sortedVariableConditions->where('contractsConditions_Modalidad', 'Descuento');
            $sortedVariableConditions = $sortedVariableConditions->where('contractsConditions_Modalidad', '!=', 'Descuento');
            $sortedVariableConditions = $sortedVariableConditions->concat($descuento);
        }
        return $sortedVariableConditions;
    }
}
