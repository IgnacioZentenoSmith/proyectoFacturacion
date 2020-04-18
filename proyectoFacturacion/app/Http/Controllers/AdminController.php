<?php

namespace App\Http\Controllers;
use App\User;
use App\Permission;
use App\Action;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
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
        return view('admin.index', compact('usuarios'));
    }
    /*
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
    */

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.create');
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
            ['name'=>'required', 'string', 'max:255'],
            ['email'=>'required', 'string', 'email', 'max:255', 'unique:users'],
            ['password'=>'required', 'string', 'min:8', 'confirmed'],
            ['role'=>'required', 'string']
        );
        $acciones = Action::all();
        
        $newUser = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);
        $newUser->save();
        $newUser->sendEmailVerificationNotification();

        $user = User::where('email', $request->email)->first();
        /*
            ACCIONES
            1 -> administracion
            3 -> clientes
            4 -> contratos
            5 -> facturas

            Vendedor -> 3 , 4
            Ejecutivo -> 3, 4, 5
            Administrador -> 1, 3, 4, 5
        */
        foreach ($acciones as $accion) {
            if ($request->role == 'Vendedor' && ($accion['idActions'] ==  1 || $accion['idActions'] ==  5)) {
                continue;
            }
            if ($request->role == 'Ejecutivo' && $accion['idActions'] ==  1) {
                continue;
            }
            $newPermission = new Permission([
                'idActions' => $accion['idActions'],
                'idUser' => $user['id']
            ]);
            $newPermission->save();
        }

        return redirect('admin')->with('success', 'Usuario agregado exitosamente.');
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
        $usuario = User::where('id', $id)->first();
        return view('admin.edit', compact('usuario'));
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
        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($user->isDirty()) {
            $user->save();
            return redirect('users')->with('success', 'Usuario editado exitosamente.');
        } else {
            return redirect('users')->with('error', 'Ha ocurrido un error.');
        }
    }

    public function editPermisos($id)
    {
        $usuario = User::where('id', $id)->first();
        $permisos = Permission::where('idUser', $id)->get();
        $permisosUsuario = $permisos->pluck('idActions')->toArray();

        $acciones = Action::all();
        return view('admin.editPermisos', compact('usuario', 'acciones', 'permisosUsuario', 'permisos'))->with('info', 'Editando al usuario: ' . $usuario['name'] . '.');
    }

    public function updatePermisos(Request $request, $id)
    {
        $acciones = $request->acciones;
        /*
        Model -> Permissions
        Action id -> idActions
        User id -> idUser
        */
        if (count($acciones) > 0) {
            //Eliminar todos los permisos y agregar los nuevos
            $permisos = Permission::where('idUser', $id)->get();
            foreach ($permisos as $permiso) {
                $permiso->delete();
            }
            foreach ($acciones as $accion) {
                $newPermission = new Permission([
                    'idActions' => $accion,
                    'idUser' => $id
                ]);
                $newPermission->save();
            }
            return redirect('admin')->with('success', 'Permisos del usuario modificadas correctamente.');
        } else {
            $permisos = Permission::where('idUser', $id)->get();
            foreach ($permisos as $permiso) {
                $permiso->delete();
                return redirect('admin')->with('success', 'Permisos del usuario eliminadas correctamente.');
            }
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
        $user = User::find($id);
        $user->delete();
        return redirect('admin')->with('success', 'Usuario eliminado exitosamente');
    }
}
