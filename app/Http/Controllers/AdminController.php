<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Token;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function showAllDataUser(Request $request)
    {
        //check admin role
        $token = explode(' ', $request->header('Authorization'));
        $jwtToken = new Token($token[1]);
        $decodedToken = JWTAuth::manager()->decode($jwtToken);
    
        // Access the token claims
        $claims = $decodedToken->getClaims();
        $isAdmin = $claims['isadmin']->getValue();

        if(!$isAdmin){
            $message = "You are not admin";
            return response()->json($message, 403);
        }
        else{
            $data = User::all();
            return response()->json($data, 200);
        }
    }

    public function showDetailDataUser(Request $request, $id)
    {
        //check admin role
        $token = explode(' ', $request->header('Authorization'));
        $jwtToken = new Token($token[1]);
        $decodedToken = JWTAuth::manager()->decode($jwtToken);

        // Access the token claims
        $claims = $decodedToken->getClaims();
        $isAdmin = $claims['isadmin']->getValue();

        if(!$isAdmin){
            $message = "You are not admin";
            return response()->json($message, 403);
        }
        else{
            $data = User::where('id_user', $id)->first();
            return response()->json($data, 200);
        }
    }
}


