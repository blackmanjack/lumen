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
        $username = $request->getUser();
        $isAdmin = DB::table('user_person')->where('username', $username)
                                        ->pluck('isadmin')
                                        ->first();

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
        $username = $request->getUser();
        $isAdmin = DB::table('user_person')->where('username', $username)
                                        ->pluck('isadmin')
                                        ->first();

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


