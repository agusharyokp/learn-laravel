<?php
namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AuthMutation
{
    public function register($rootValue, array $args)
    {
        $user = User::create([
            'firstname' => $args['firstname'],
            'lastname' => $args['lastname'],
            'name' => $args['firstname'] . ' ' . $args['lastname'],
            'email' => $args['email'],
            'password' => Hash::make($args['password']),
        ]);

        return $user;
    }

    public function login($rootValue, array $args)
    {
        $credentials = ['email' => $args['email'], 'password' => $args['password']];

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            throw new \Exception('Invalid credentials');
        }

        return [
            'token' => $token,
            'user' => Auth::guard('api')->user(),
        ];
    }
}
