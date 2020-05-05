<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Auth;
use App\Permission;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Hash;

class VerificationController extends Controller
{
    use VerifiesEmails;

    /**
     * Show the email verification notice.
     *
     */
    public function show()
    {
        //
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();

        $usuario = $request->user();
        // ->route('id') gets route user id and getKey() gets current user id() 
        // do not forget that you must send Authorization header to get the user from the request
        if ($request->route('id') == $usuario->getKey() &&
            $usuario->markEmailAsVerified()) {
            event(new Verified($usuario));
        }
        return view('emails.verify', compact('usuario', 'authPermisos'))->with('success', 'Email verificado exitosamente!');
        //return redirect($this->redirectPath());
    }

    public function setPassword(Request $request, $id) 
    {
        $userId = Auth::user()->id;
        $authPermisos = Permission::where('idUser', $userId)->get();
        $authPermisos = $authPermisos->pluck('idActions')->toArray();

        $request->validate(
            ['password'=>'required', 'string', 'min:8', 'confirmed']
        );

        $user = User::find($id);
        $user->password = Hash::make($request->password);

        $user->save();
        return view('home.index', compact('authPermisos'))->with('success', 'Contraseña establecida correctamente.');
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json('Usuario ya ha verificado su email!', 422);
//            return redirect($this->redirectPath());
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json('Se ha re enviado la confirmación de email!');
//        return back()->with('resent', true);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }
}