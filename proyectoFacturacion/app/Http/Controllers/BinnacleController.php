<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Binnacle;
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
use Laravel\Ui\Presets\React;

class BinnacleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();
        $binnacles = Binnacle::whereNotNull('binnacle_action')
        ->join('users', 'users.id', '=', 'binnacle.idUser')
        ->select('binnacle.*', 'users.name as userName')
        ->get();
        foreach ($binnacles as $binnacle) {
            if ($binnacle->binnacle_tablePreValues != null) {
                // convert json to array
                $arrayPreValues = json_decode($binnacle->binnacle_tablePreValues, true);
                //  create a new collection instance from the array
                $binnacle->binnacle_tablePreValues = collect($arrayPreValues);
            }
            if ($binnacle->binnacle_tablePostValues != null) {
                // convert json to array
                $arrayPostValues = json_decode($binnacle->binnacle_tablePostValues, true);
                //  create a new collection instance from the array
                $binnacle->binnacle_tablePostValues = collect($arrayPostValues);
            }
        }
        $uniqueUsers = $binnacles->unique(function ($item) {
            return $item['userName'];
        });

        $uniqueActions = $binnacles->unique(function ($item) {
            return $item['binnacle_action'];
        });

        $uniqueTables = $binnacles->unique(function ($item) {
            return $item['binnacle_tableName'];
        });

        return view('binnacle.index', compact('authPermisos', 'binnacles', 'uniqueUsers', 'uniqueActions', 'uniqueTables'));
    }

    public function filteredIndex(Request $request) {
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();
        $binnacles = Binnacle::whereNotNull('binnacle_action')
        ->join('users', 'users.id', '=', 'binnacle.idUser')
        ->select('binnacle.*', 'users.name as userName')
        ->get();
        foreach ($binnacles as $binnacle) {
            if ($binnacle->binnacle_tablePreValues != null) {
                // convert json to array
                $arrayPreValues = json_decode($binnacle->binnacle_tablePreValues, true);
                //  create a new collection instance from the array
                $binnacle->binnacle_tablePreValues = collect($arrayPreValues);
            }
            if ($binnacle->binnacle_tablePostValues != null) {
                // convert json to array
                $arrayPostValues = json_decode($binnacle->binnacle_tablePostValues, true);
                //  create a new collection instance from the array
                $binnacle->binnacle_tablePostValues = collect($arrayPostValues);
            }
        }
        $uniqueUsers = $binnacles->unique(function ($item) {
            return $item['userName'];
        });

        $uniqueActions = $binnacles->unique(function ($item) {
            return $item['binnacle_action'];
        });

        $uniqueTables = $binnacles->unique(function ($item) {
            return $item['binnacle_tableName'];
        });


        $request->validate([
            'filter_usuarios'=> 'numeric|nullable',
            'filter_actions'=> 'string|max:50|nullable',
            'filter_tables'=> 'string|max:50|nullable',
            'filter_fecha_desde'=> 'date|nullable',
            'filter_fecha_hasta'=> 'date|nullable',
        ]);
        if (!is_null($request->filter_usuarios)) {
            $binnacles = $binnacles->where('idUser', $request->filter_usuarios);
        }
        if (!is_null($request->filter_actions)) {
            $binnacles = $binnacles->where('binnacle_action', $request->filter_actions);
        }
        if (!is_null($request->filter_tables)) {
            $binnacles = $binnacles->where('binnacle_tableName', $request->filter_tables);
        }
        if (!is_null($request->filter_fecha_desde)) {
            $binnacles = $binnacles->where('created_at', '>=', $request->filter_fecha_desde);
        }
        if (!is_null($request->filter_fecha_hasta)) {
            $binnacles = $binnacles->where('created_at', '<=', $request->filter_fecha_hasta);
        }

        return view('binnacle.index', compact('authPermisos', 'binnacles', 'uniqueUsers', 'uniqueActions', 'uniqueTables'));
    }

    /**
     * Create a new trait instance.
     * $action -> Accion de la tabla
     * $tableName -> Nombre de la tabla
     * $tableId -> ID afectado de la tabla
     * $preValues -> valores pre de la accion
     * $postValues -> valores post de la accion
     * @return void
     */

    public function reportBinnacle($action, $tableName, $tableId, $preValues = null, $postValues = null) {
        /*
        CREATE -> $preValues = null, $postValues = model
        UPDATE -> $preValues = preModel, $postValues = postModel
        DELETE -> $preValues = model, $postValues = null
        */
        $userId = Auth::user()->id;
        $newBinnacle = new Binnacle([
            'idUser' => $userId,
            'binnacle_action' => $action,
            'binnacle_tableName' => $tableName,
            'binnacle_tableId' => $tableId,
            'binnacle_tablePreValues' => $preValues,
            'binnacle_tablePostValues' => $postValues,
        ]);
        $newBinnacle->save();
    }
}
