<?php

namespace App\Http\Controllers;
use App\User;
use App\Permission;
use App\Action;

use Illuminate\Http\Request;

class AdminController extends Controller
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
        $usuarios = User::all();
        $permisos = Permission::all();
        $acciones = Action::all();
        $testJoin = Action::join('permissions', 'actions.idActions', '=', 'permissions.idActions')->select('actions.idActions', 'actions.actionName', 'permissions.idUser')->get();
        return view('admin.index', compact('usuarios', 'permisos', 'acciones', 'testJoin'));
    }
    public function roles()
    {
        $usuarios = User::all();
        $permisos = Permission::all();
        $acciones = Action::all();
        return view('admin.roles', compact('usuarios', 'permisos', 'acciones'));
    }

    public function ajaxUpdateUserActions(Request $request) {
        
        $permisosRequest = $request->data;
        Permission::whereNotNull('idPermissions')->delete();

        foreach ($permisosRequest as $permisos) {
            if ($permisos) {
                $action = Action::where('actionName', $permisos['accion'])->first();
                $permission = new Permission([
                    'idUser'=>$permisos['idUser'],
                    'idActions'=>$action->idActions
                ]);
                $permission->save();
            }
        }
        $response = array(
            'status' => 'success',
            'msg' => $permisosRequest,
        );
        return response()->json($response);
    }

    public function ajaxUpdateRoleActions(Request $request) {
        $input = $request->all();
        return response()->json(['success'=>'Got Simple Ajax Request.']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
