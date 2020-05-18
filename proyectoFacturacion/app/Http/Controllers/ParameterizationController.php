<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Modules;
use App\PaymentUnits;
use Auth;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class ParameterizationController extends Controller
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
      if (in_array(12, $authPermisos) && in_array(13, $authPermisos)) {
        return redirect()->action('ParameterizationController@modulesIndex');
      }
      elseif (in_array(12, $authPermisos) && in_array(14, $authPermisos)) {
        return redirect()->action('ParameterizationController@paymentunitsIndex');
      }
      else { 
        return redirect()->action('HomeController@index');
      }
    }

    public function modulesIndex()
    {
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();
        $modules = Modules::all();
        $paymentUnits = PaymentUnits::all();
        return view('parameterization.modules', compact('modules', 'paymentUnits', 'authPermisos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function modulesCreate()
    {
      
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();
        $modulesPadres = Modules::all();
        return view('parameterization.modulesCreate', compact('authPermisos', 'modulesPadres'));
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function modulesStore(Request $request)
    {
      
        $request->validate([
            'moduleName'=>'required|string|max:100|unique:modules,moduleName' ,
            'moduleParentId' => 'required_if:hasParent,si',
        ]);

        if ($request->hasParent == 'no') {
            $moduleParentId = null;
        }
        else if ($request->hasParent == 'si') {
            $moduleParentId = $request->moduleParentId;
        }

        
        $newModules = new Modules([
            'moduleName' => $request->moduleName,
            'moduleParentId' => $request->moduleParentId,
        ]);
        $newModules->save();

        return redirect()->action('ParameterizationController@modulesIndex')->with('success', 'Módulo agregado exitosamente.');
        
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
    public function modulesEdit($id)
    {
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();
        $module = Modules::where('id', $id)->first();
        $modulesPadres = Modules::all();
        return view('parameterization.modulesEdit', compact('module', 'modulesPadres', 'authPermisos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function modulesUpdate(Request $request, $id)
    {
      $request->validate([
        'moduleName'=>'required|string|max:100|unique:modules,moduleName,' .$id ,
        'moduleParentId' => 'required_if:hasParent,si',
      ]);
      if ($request->hasParent == 'no') {
          $moduleParentId = null;
      }
      else if ($request->hasParent == 'si') {
          $moduleParentId = $request->moduleParentId;
      }

        $modulo = Modules::find($id);
        $modulo->moduleName = $request->moduleName;
        $modulo->moduleParentId = $request->moduleParentId;

        if ($modulo->isDirty()) {
            $modulo->save();
            return redirect()->action('ParameterizationController@modulesIndex')->with('success', 'Módulo editado exitosamente.');
        } else {
          return redirect()->action('ParameterizationController@modulesIndex');
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function modulesDestroy($id)
    {
        $modulo = Modules::find($id);
        $moduloIDs = $this->getAllChildren($id);
        $moduloIDs = $moduloIDs->pluck('id');
        if ($moduloIDs->count() > 0) {
          return redirect()->action('ParameterizationController@modulesIndex')->with('error', 'Módulos con hijos no pueden ser eliminados, por favor elimine sus hijos primero.');
        } else {
            $modulo->delete();
          return redirect()->action('ParameterizationController@modulesIndex')->with('success', 'Módulo eliminado exitosamente');
        }
    }

    public function getAllChildren($id) 
    {
        $childrenModulesIds = new Collection();
        $childrens = Modules::where('moduleParentId',$id)->get();
        foreach ($childrens as $children) {
            $childrenModulesIds->push($children);
            $childrenModulesIds = $childrenModulesIds->merge($this->getAllChildren($children['id']));
        }
        return $childrenModulesIds;
    }

    // CRUD PAYMENT UNITS
    public function paymentunitsIndex()
    {
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();
        $paymentUnits = PaymentUnits::all();
        return view('parameterization.paymentunits', compact('paymentUnits', 'authPermisos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function paymentunitsCreate()
    {
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();
        return view('parameterization.paymentunitsCreate', compact('authPermisos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function paymentunitsStore(Request $request)
    {
      $request->validate([
        'payment_units'=>'required|string|max:100|unique:payment_units,payment_units',
      ]);
        $newPaymentUnit = new PaymentUnits([
            'payment_units' => $request->payment_units,
        ]);
        $newPaymentUnit->save();
        return redirect()->action('ParameterizationController@paymentunitsIndex')->with('success', 'Unidad de pago agregado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function paymentunitsEdit($id)
    {
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();
        $paymentUnit = PaymentUnits::where('id', $id)->first();
        return view('parameterization.paymentunitsEdit', compact('paymentUnit', 'authPermisos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function paymentunitsUpdate(Request $request, $id)
    {
      $request->validate([
        'payment_units'=>'required|string|max:100|unique:payment_units,payment_units,' .$request->payment_units ,
      ]);

        $paymentUnit = PaymentUnits::find($id);
        $paymentUnit->payment_units = $request->payment_units;

        if ($paymentUnit->isDirty()) {
            $paymentUnit->save();
            return redirect()->action('ParameterizationController@paymentunitsIndex')->with('success', 'Unidad de pago editada exitosamente.');
        } else {
          return redirect()->action('ParameterizationController@paymentunitsIndex');
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function paymentunitsDestroy($id)
    {
      $paymentUnit = PaymentUnits::find($id);
      $paymentUnit->delete();
      return redirect()->action('ParameterizationController@paymentunitsIndex')->with('success', 'Unidad de pago eliminada exitosamente');
    }
}
