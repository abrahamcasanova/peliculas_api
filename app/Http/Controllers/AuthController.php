<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Carbon\Carbon;
use App\UserDevice;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use App\Services\FirebaseService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    /**
     * Registro de usuario
     */
    public function signUp(Request $request)
    {
        
        //$factory = (new Factory)->withServiceAccount("/Applications/MAMP/htdocs/Appnovasolutions/zeus_api/app/Services/zeus-c5912-090dc31094f7.json");
        //$auth = $factory->createAuth();

        $user = User::where('email_adminsyf',$request->email)->get();
        if(isset($user) && count($user) <= 0){
            return response()->json([
                'message' => 'Error, no se encuentra en la base de datos',
            ], 401);
        }

        $factory = new FirebaseService();
        $auth = $factory->serviceUser();

        $userProperties = [
            'email' => $request->email,
            'emailVerified' => false,
            'phoneNumber' => null,
            'password' => $request->password,
            'displayName' => $user->first()->name,
            'disabled' => false,
        ];
        
        try {
            $createdUser = $auth->createUser($userProperties);
        } catch (\Kreait\Firebase\Exception\Auth\EmailExists | \Kreait\Firebase\Exception\Auth\InvalidPassword | \Kreait\Firebase\Exception\InvalidArgumentException | \Kreait\Firebase\Auth\SignIn\FailedToSignIn $e) {
            $message = $e->getMessage();
            return response()->json([
                'message' => 'Error, el usuario ya existe, intenta con restablecer tu contraseña',
                'firebase_message' => $message
            ], 401);
        }

        
        if($createdUser){
            foreach ($user as $key => $value) {
                $userDevice = UserDevice::updateOrCreate(
                    ['id_adminsyf' => $value->id_adminsyf, 
                    'uuid_device' => $request->tokenFCM
                ],$request->all());
                $value->password = Hash::make($request->password);
                $value->save();
            }
        }
        
        /*User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);*/

        return response()->json([
            'message' => 'Usuario creado correctamente!',
            'data'    => $user->pluck('id_adminsyf')
        ], 200);
    }

    /**
     * Inicio de sesión y creación de token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        $credentials = request(['email', 'password']);
        
        if (!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Usuario y/o contraseña incorrectos'
            ], 401);

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        $roles = $user->roles->pluck('name');

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString(),
            'user' => $user,
            'roles' => $roles
            //'roles' => $user->roles->toArray()
        ]);
    }

    public function show(User $user){
        return response()->json($user);
    }

    /**
     * Cierre de sesión (anular el token)
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Obtener el objeto User como json
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}