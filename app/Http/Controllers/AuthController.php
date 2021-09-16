<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Models\User;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'username' => 'required|unique:users|string',
            'email' => 'required|unique:users,email',
            'password' => 'required',
            'user_type' => new EnumValue(UserType::class),
        ]);

        $userType = array_key_exists('user_type', $fields) ? $fields['user_type'] : UserType::Customer;

        $user = User::create([
            'name' => $fields['name'],
            'username' => $fields['username'],
            'email' => $fields['email'],
            'user_type' => $userType,
            'password' => bcrypt($fields['password']),
        ]);

        // This token will be used for authorization afterwards
        $token = $user->createToken('apptoken')->plainTextToken;
        $response = [
            'user' => $user,
            'login_token' => $token
        ];

        $cookie = cookie('loginToken', $token, 60*24);

        return response($response, 201)->withCookie($cookie);
    }



    public function login(Request $request)
    {
        $fields = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);


        $user = User::where('username', $fields['username'])->first();

        if(!$user) {
            return response([
                'invalidUsername' => 'The username is invalid'
            ], ResponseAlias::HTTP_FORBIDDEN);
        }
        if(!Hash::check($fields['password'], $user->password)) {
            return response([
                'invalidPassword' => 'Invalid Password',
            ], ResponseAlias::HTTP_FORBIDDEN);
        }



        // This token will be used for authorization afterwards
        $token = $user->createToken('apptoken')->plainTextToken;
        $response = [
            'user' => $user,
            'login_token' => $token
        ];

        $cookie = cookie('loginToken', $token, 60*24);


        return response($response, 201)->withCookie($cookie);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return [
            'info' => 'logged out successfully!',
        ];
    }
}
