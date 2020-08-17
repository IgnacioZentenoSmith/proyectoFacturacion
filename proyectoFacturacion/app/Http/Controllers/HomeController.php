<?php

namespace App\Http\Controllers;

use App\Permission;

use Auth;
use App\Jobs\ProcessApis;
use Illuminate\Support\Facades\DB;

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
        $apiJobQueue = DB::table('jobs')->where('queue', 'api')->get();
        if ($apiJobQueue->count() == 0) {
            ProcessApis::dispatch()->onQueue('api');
        }
        return view('home.index', compact('authPermisos'));
    }
}
