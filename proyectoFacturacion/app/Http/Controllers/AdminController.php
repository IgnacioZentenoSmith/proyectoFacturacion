<?php

namespace App\Http\Controllers;
use App\User;
use App\Permission;
use App\Action;

use Auth;

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
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();
        $usuarios = User::all();
        return view('admin.index', compact('usuarios', 'authPermisos'));
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
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();
        return view('admin.create', compact('authPermisos'));
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
            'name'=>'required|string|max:255',
            'email'=>'required|email:rfc,dns,spoof|unique:users,email',
            'role'=>'required|string'
        ]);
        $acciones = Action::all();
        
        $newUser = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->email),
            'role' => $request->role,
            'status' => 'Activo'
        ]);
        $newUser->save();
        $newUser->sendEmailVerificationNotification();

        $user = User::where('email', $request->email)->first();
        /*
            ACCIONES
            1 -> Administracion     Menu
            2 -> Clientes           Menu
            3 -> Contratos          Menu
            4 -> Facturas           Menu

            5 -> Administracion_create           Programa       1
            6 -> Administracion_edit             Programa       1
            7 -> Administracion_editPermisos     Programa       1
            8 -> Administracion_delete           Programa       1


            Vendedor -> 2 , 3
            Ejecutivo -> 2, 3, 4
            Administrador -> 1, 2, 3, 4, 5, 6, 7, 8
        */
        foreach ($acciones as $accion) {
            if ($request->role == 'Vendedor' && ($accion['id'] !=  2 && $accion['id'] !=  3)) {
                continue;
            }
            if ($request->role == 'Ejecutivo' && ($accion['id'] !=  2 && $accion['id'] !=  3 && $accion['id'] !=  4)) {
                continue;
            }
            $newPermission = new Permission([
                'idActions' => $accion['id'],
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
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();
        $usuario = User::where('id', $id)->first();
        return view('admin.edit', compact('usuario', 'authPermisos'));
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
        $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email:rfc,dns,spoof|unique:users,email,'.$id,
            'role'=>'required|string'
        ]);

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($user->isDirty()) {
            $user->save();
            return redirect('admin')->with('success', 'Usuario editado exitosamente.');
        } else {
            return redirect('admin')->with('error', 'Ha ocurrido un error.');
        }
    }

    public function editPermisos($id)
    {
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();

        $usuario = User::where('id', $id)->first();
        $permisos = Permission::where('idUser', $id)->get();
        $permisosUsuario = $permisos->pluck('idActions')->toArray();

        $acciones = Action::all();
        return view('admin.editPermisos', compact('usuario', 'acciones', 'permisosUsuario', 'authPermisos'))->with('info', 'Editando al usuario: ' . $usuario['name'] . '.');
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


    public function changeStatus($id)
    {
        $user = User::find($id);
        if ($user->status == 'Activo') {
            $user->status = 'Inactivo';
        }
        elseif ($user->status == 'Inactivo' || $user->status == '') {
            $user->status = 'Activo';
        } else {
            return redirect('admin')->with('danger', 'Ha ocurrido un error.');
        }
        $user->save();
        return redirect('admin')->with('success', 'Status del usuario modificado correctamente.');
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
