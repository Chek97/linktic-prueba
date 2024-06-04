<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login(Request $request){
        
        $validate = Validator::make($request->all(),[
            'email' => 'required',
            'password' => 'required'
        ]);

        if($validate->fails()){
            return response()->json([
                "status" => false,
                "message" => $validate->errors()
            ], 400);
        }

        $credentials = request(['email', 'password']);

        if(!$token = auth()->attempt($credentials)){
            return response()->json([
                'status' => false,
                'error' => 'Credenciales incorrectas'
            ], 401);
        }

        return $this->respondWithToken($token);
        
    }

    public function register(Request $request){

        $validate = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required'
        ]);

        if($validate->fails()){
            return response()->json([
                "status" => false,
                "message" => $validate->errors()
            ], 400);
        }

        $user = new User();

        $user->email = $request->email;
        $user->name = $request->name;
        $user->password = Hash::make($request->password);

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Usuario creado con exito',
            'user' => $user
        ], 201);
    }

    protected function respondWithToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60 // * en esta linea muestra error pero es por un tema de libreria, en funcionalidad funciona
        ]);
    }
}
