<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;





class UserController extends Controller
{
   
    
    public function register(Request $request)
    {
        $data = $request->json()->all();
        $itExistsUserName=User::where('email',$data['email'])->first();

        if ($itExistsUserName==null) {
            $user = User::create(
                [
                    'name'=>$data['name'],
                    'email'=>$data['email'],
                    'password'=>Hash::make($data['password'])

                ]
            );
            $token = $user->createToken('web')->plainTextToken;
                return response()->json([
                    'data'=>$user,
                    'token'=> $token

                ],200);// tiempo de respuesta, si excede marca un error
        } else {
               return response()->json([
                'data'=>'User already exists!',
                'status'=> false
            ],200);
       }

   }

     public function login(Request $request){

        if(!Auth::attempt($request->only('email','password')))
        {
            
            return response()->json
            ([
                'message'=> 'Correo o contraseña incorrectos',
                'status'=> false
            ],400);
        }
         $user = User::where('email',$request['email'])->firstOrFail();
         $token = $user->createToken('web')->plainTextToken;
    
         return response()->json
         ([
            'data'=> $user,
            'token'=>$token
         ]);
    
       }

   public function logout(Request $request)
   {
    $request->user()->currentAccessToken()->delete();
    return response()->json
    ([
        'status'=> true,
    ]);

   }

    public function showById($id)
    {
        $user = User::find($id);
        
        return response()->json(["data"=>$user]);
    }


    public function newPassword($correoElectronico)
    {
        $usuario = User::where('email', $correoElectronico)->first();
        if (!$usuario) 
        {
            return response()->json(['message' => 'El usuario no existe'], 200);
        }
        else
        {
        $nuevaPassword = Str::random(7);
        
        $usuario->password = Hash::make($nuevaPassword);
        $usuario->save();
        
        return response()->json([
            'mensaje' => 'Contraseña actualizada!!!',
            'nueva_password' => $nuevaPassword,
            
        ], 200);
        }

        
    }   
    
  
}
