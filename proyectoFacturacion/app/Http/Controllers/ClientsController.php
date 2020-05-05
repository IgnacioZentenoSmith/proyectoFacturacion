<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Client;
use Auth;

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
        $clientes = Client::all();
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
        $clientesPadre = Client::all();
        return view('clients.create', compact('authPermisos', 'clientesPadre'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(
            ['clientName'=>'required', 'string', 'max:200'],
            ['clientRazonSocial'=>'required', 'string', 'max:200'],
            ['clientRUT'=> 'required', 'string', 'max:200', 'unique:clients'],
            ['clientParentId' => 'required_if:hasParent,si','numeric','between:1,9999'],
        );
        if ($request->hasParent == 'no') {
            $clientParentId = null;
        }
        else if ($request->hasParent == 'si') {
            $clientParentId = $request->clientParentId;
        }

        
        $newClient = new Client([
            'clientName' => $request->clientName,
            'clientRazonSocial' => $request->clientRazonSocial,
            'clientRUT' => $request->clientRUT,
            'clientParentId' => $clientParentId,
        ]);
        $newClient->save();

        return redirect('clients')->with('success', 'Cliente agregado exitosamente.');
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
        $clientesPadre = Client::all();
        return view('clients.edit', compact('cliente', 'clientesPadre', 'authPermisos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate(
            ['clientName'=>'required', 'string', 'max:200'],
            ['clientRazonSocial'=>'required', 'string', 'max:200'],
            ['clientRUT'=> 'required', 'string', 'max:200', 'unique:clients'],
            ['clientParentId' => 'required_if:hasParent,si','numeric','between:1,9999'],
        );
        if ($request->hasParent == 'no') {
            $clientParentId = null;
        }
        else if ($request->hasParent == 'si') {
            $clientParentId = $request->clientParentId;
        }

        $cliente = Client::find($id);
        $cliente->clientName = $request->clientName;
        $cliente->clientRazonSocial = $request->clientRazonSocial;
        $cliente->clientRUT = $request->clientRUT;
        $cliente->clientParentId = $clientParentId;

        if ($cliente->isDirty()) {
            $cliente->save();
            return redirect('clients')->with('success', 'Cliente editado exitosamente.');
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
            return redirect('clients')->with('error', 'Clientes con hijos no pueden ser eliminados, por favor elimine sus hijos primero.');
        } else {
            $cliente->delete();;
            return redirect('clients')->with('success', 'Cliente eliminado exitosamente');
        }
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
}
