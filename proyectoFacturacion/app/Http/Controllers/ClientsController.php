<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Client;
use App\User;
use App\Binnacle;
use Auth;
use Illuminate\Support\Arr;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();
        $clientes = Client::whereNull('clientParentId')->get();

        foreach ($clientes as $cliente) {
            //Obtener nombre de sus ejecutivos
            if ($cliente->idUser != null) {
                $ejecutivo = User::find($cliente->idUser);
                if ($ejecutivo) {
                    $ejecutivoNombre = $ejecutivo->name;
                    $cliente = Arr::add($cliente, 'ejecutivoNombre', $ejecutivoNombre);
                } else {
                    $cliente->idUser = null;
                    $cliente->save();
                }
            }
            //Obtener sus hijos
            $getChildren = $this->getAllChildren($cliente->id);
            $childrenNumber = count($getChildren);
            $cliente = Arr::add($cliente, 'clientChildrenCount', $childrenNumber);
        }
        return view('clients.index', compact('clientes', 'authPermisos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();
        $ejecutivos = User::where('role', 'Ejecutivo')->get();
        return view('clients.create', compact('ejecutivos', 'authPermisos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'clientRazonSocial'=> 'required|string|max:100',
            'clientContactEmail'=> 'email:dns|nullable',
            'clientPhone'=> 'string|max:100|nullable',
            'clientDirection'=> 'string|max:100|nullable',
            'clientBusinessActivity'=> 'string|max:100|nullable',
            'idEjecutivo'=> 'required|numeric|min:1',
        ]);

        $newClient = new Client([
            'clientRazonSocial' => $request->clientRazonSocial,
            'clientRUT' => null,
            'clientParentId' => null,
            'clientContactEmail' => $request->clientContactEmail,
            'clientPhone' => $request->clientPhone,
            'clientDirection' => $request->clientDirection,
            'clientBusinessActivity' => $request->clientBusinessActivity,
            'idUser'=> $request->idEjecutivo,
            'clientTipoEmpresa' => 'Holding',
        ]);
        $newClient->save();
        app('App\Http\Controllers\BinnacleController')->reportBinnacle('CREATE', $newClient->getTable(), $newClient->clientRazonSocial, null, $newClient);

        return redirect('clients')->with('success', 'Holding agregado exitosamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();
        $cliente = Client::where('id', $id)->first();
        $ejecutivos = User::where('role', 'Ejecutivo')->get();
        return view('clients.edit', compact('cliente', 'ejecutivos', 'authPermisos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //UPDATE HOLDING
    public function update(Request $request, $id)
    {
        $request->validate([
            'clientRazonSocial'=> 'required|string|max:100',
            'clientContactEmail'=> 'email:dns|nullable',
            'clientPhone'=> 'string|max:100|nullable',
            'clientDirection'=> 'string|max:100|nullable',
            'clientBusinessActivity'=> 'string|max:100|nullable',
            'idEjecutivo'=> 'required|numeric|min:1',
        ]);

        $cliente = Client::find($id);


        $cliente->clientRazonSocial = $request->clientRazonSocial;
        $cliente->clientContactEmail = $request->clientContactEmail;
        $cliente->clientPhone = $request->clientPhone;
        $cliente->clientDirection = $request->clientDirection;
        $cliente->clientBusinessActivity = $request->clientBusinessActivity;
        $cliente->idUser = $request->idEjecutivo;
        $cliente->clientTipoEmpresa = 'Holding';

        if ($cliente->isDirty()) {
            $preCliente = Client::find($id);
            app('App\Http\Controllers\BinnacleController')->reportBinnacle('UPDATE', $cliente->getTable(), $cliente->clientRazonSocial, $preCliente, $cliente);

            $cliente->save();
            return redirect('clients')->with('success', 'Holding editado exitosamente.');
        } else {
            return redirect('clients');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cliente = Client::find($id);
        $clientsIDs = $this->getAllChildren($id);
        $clientsIDs = $clientsIDs->pluck('id');
        if ($clientsIDs->count() > 0) {
            return redirect('clients')->with('error', 'Este holding no ha podido ser eliminado, por favor elimine todas sus razones sociales dependientes primero.');
        } else {
            app('App\Http\Controllers\BinnacleController')->reportBinnacle('DELETE', $cliente->getTable(), $cliente->clientRazonSocial, $cliente, null);
            $cliente->delete();
            return redirect('clients')->with('success', 'Cliente eliminado exitosamente');
        }
    }

    public function childrenIndex($idCliente) {
        $authPermisos = $this->getPermisos();
        $holding = Client::find($idCliente);
        $children = Client::where('clientParentId', $idCliente)->get();
        return view('clients.childrenIndex', compact('authPermisos', 'holding', 'children'));
    }

    public function childrenCreate($idCliente) {
        $authPermisos = $this->getPermisos();
        $holding = Client::find($idCliente);
        return view('clients.childrenCreate', compact('authPermisos', 'holding'));
    }

    public function childrenStore(Request $request, $idCliente) {
        $authPermisos = $this->getPermisos();

        $request->validate([
            'clientRazonSocial'=> 'required|string|max:100',
            'clientRUT'=> 'required|string|max:200|unique:clients,clientRUT',
            'clientContactEmail'=> 'email:dns|nullable',
            'clientPhone'=> 'string|max:100|nullable',
            'clientDirection'=> 'string|max:100|nullable',
            'clientBusinessActivity'=> 'string|max:100|nullable',
        ]);
        $formattedRUT = $this->formatRut($request->clientRUT);

        $newClient = new Client([
            'clientRazonSocial' => $request->clientRazonSocial,
            'clientRUT' => $formattedRUT,
            'clientParentId' => $idCliente,
            'clientContactEmail' => $request->clientContactEmail,
            'clientPhone' => $request->clientPhone,
            'clientDirection' => $request->clientDirection,
            'clientTipoEmpresa' => 'Empresa',
        ]);
        $newClient->save();
        app('App\Http\Controllers\BinnacleController')->reportBinnacle('CREATE', $newClient->getTable(), $newClient->clientRazonSocial, null, $newClient);
        return redirect()->action('ClientsController@childrenIndex', ['idCliente' => $idCliente])->with('success', 'Cliente agregado exitosamente.');
    }

    public function childrenEdit($idCliente, $idHijo) {
        $authPermisos = $this->getPermisos();
        $holding = Client::find($idCliente);
        $hijo = Client::find($idHijo);
        return view('clients.childrenEdit', compact('authPermisos', 'holding', 'hijo'));
    }

    public function childrenUpdate(Request $request, $idCliente, $idHijo) {
        $authPermisos = $this->getPermisos();

        $request->validate([
            'clientRazonSocial'=> 'required|string|max:100',
            'clientRUT'=> 'required|string|max:200|unique:clients,clientRUT,'.$idHijo,
            'clientContactEmail'=> 'email:dns|nullable',
            'clientPhone'=> 'string|max:100|nullable',
            'clientDirection'=> 'string|max:100|nullable',
            'clientBusinessActivity'=> 'string|max:100|nullable',
        ]);

        $hijo = Client::find($idHijo);
        $preHijo = $hijo;

        $formattedRUT = $this->formatRut($request->clientRUT);

        $hijo->clientRazonSocial = $request->clientRazonSocial;
        $hijo->clientRUT = $formattedRUT;
        $hijo->clientContactEmail = $request->clientContactEmail;
        $hijo->clientPhone = $request->clientPhone;
        $hijo->clientDirection = $request->clientDirection;
        $hijo->clientTipoEmpresa = 'Empresa';
        if ($hijo->isDirty()) {
            $preHijo = Client::find($idHijo);
            app('App\Http\Controllers\BinnacleController')->reportBinnacle('UPDATE', $hijo->getTable(), $hijo->clientRazonSocial, $preHijo, $hijo);
            $hijo->save();
            return redirect()->action('ClientsController@childrenIndex', ['idCliente' => $idCliente])->with('success', 'Cliente editado exitosamente.');
        } else {
            return redirect()->action('ClientsController@childrenIndex', ['idCliente' => $idCliente]);
        }
    }

    public function childrenDestroy($idCliente, $idHijo) {
        //DIE
        $authPermisos = $this->getPermisos();
        $hijo = Client::find($idHijo);
        app('App\Http\Controllers\BinnacleController')->reportBinnacle('DELETE', $hijo->getTable(), $hijo->clientRazonSocial, $hijo, null);
        $hijo->delete();
        return redirect()->action('ClientsController@childrenIndex', ['idCliente' => $idCliente])->with('success', 'Cliente eliminado exitosamente.');
    }

    public function getAllChildren($id) {
        $childrenClientIds = new Collection();
        $childrens = Client::where('clientParentId',$id)->get();
        foreach ($childrens as $children) {
            $childrenClientIds->push($children);
            $childrenClientIds = $childrenClientIds->merge($this->getAllChildren($children['id']));
        }
        return $childrenClientIds;
    }

    public function getPermisos() {
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();
        return $authPermisos;
    }

    public function formatRut($rut) {
        $regexRut = preg_replace('/[^0-9K]/', '', $rut);
        $formattedRUT = substr($regexRut, 0, strlen($regexRut) - 1) . "-" . substr($regexRut, -1);
        return $formattedRUT;
    }
}
