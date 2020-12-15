<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Exception;

class UserController extends Controller
{
    public function login(Request $request) { 
        $validator = Validator::make($request->all(), 
            [
                'name' => 'alpha_dash|max:255',
                'email' => 'required|max:255',
                'password' => 'required',
            ],
            [
                'required' => ':attribute cannot be empty.',
                'alpha_dash' => ':attribute cannot contain spaces.',
            ], 
            [
                'name' => 'Username',
                'email' => 'Email address',
                'password' => 'Password',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 404);
        }

        if(User::where('email', $request->email)->exists()){
            $user = User::where('email', $request->email)->where('deleted_at', NULL)->first();
            if($user->exists()){
                if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                    $token = Auth::user()->createToken('login')->accessToken;
                    return response()->json([
                        'message' => 'Successful!',
                        'user' => $user,
                        'access_token' => $token
                    ], 200);
                } else {
                    return response()->json(['message' => 'Incorrect password.'], 404);
                }
            } else {
                return response()->json(['message' => 'Account has deleted.'], 404);
            }
        } else {
            return response()->json(['message' => 'Email doesn\'t exists.'], 404);
        }
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), 
            [
                'name' => 'required|alpha_dash|unique:users|max:255',
                'email' => 'required|unique:users|max:255',
                'password' => 'required',
            ],
            [
                'required' => ':attribute cannot be empty.',
                'alpha_dash' => ':attribute cannot contain spaces, dots, special characters.',
                'unique' => ':attribute has exists.',
            ], 
            [
                'name' => 'Username',
                'email' => 'Email address',
                'password' => 'Password',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 404);
        }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json(['message' => 'Successful'], 200);
    }

    public function logout(Request $request) {
        if(Auth::user()) {
            $token = $request->user()->token();
            $token->revoke();
            return response()->json(['message' => 'Logout successful.'], 200);
        } else {
            return response()->json(['message' => 'Not logged in.'], 200);
        }
    }
}
        