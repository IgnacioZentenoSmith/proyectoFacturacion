<?php

namespace App\Http\Controllers;

use App\Permission;
use App\ContractPaymentDetails;
use Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
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
        return view('home.index', compact('authPermisos'));
    }

    public function GCI_Api() {
        $response = Http::withHeaders([
            'api_key' => '1234567890',
        ])->get('https://comercialinmobiliarias.cl/gci_desarrollo/wpenaloza/api/');

        return $response->json();
    }
    public function PVI_Api() {
        $response = Http::withHeaders([
            'api_key' => '6b4c17219b48f933cc4c7caf69226d46e2b91ffd',
        ])->get('http://pvi.cl/facturacion/test.php');
        return $response->json();
    }
    public function ETDTP_Api() {
        $response = Http::withHeaders([
            'key' => '35328fcd1b8cf9e101fc0e398de0be08',
        ])->get('https://www.pok.cl/apirest/post.php');
        return $response->json();
    }
    public function LICITA_Api() {
        $response = Http::withHeaders([
            'key' => '3d524a53c110e4c22463b10ed32cef9d',
        ])->get('http://10.33.51.203/v3/restapi_licita_v1/post.php');
        return $response->json();
    }
}
