<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
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

    public function create(Request $request)
    {
        $data = new User();
        $data->username = $request->username;
        $data->password = $request->password;
        $data->email = $request->email;
        $data->token = Str::random(32);
        $data->status = 0;
        $data->is_admin = 0;

        if($data->email === '' || $data->password === '' || $data->username === ''){
            $message = 'Parameter mustn\'t empty';
            return response()->json($message, 400);
        }
        $save = $data->save();

        // $dataEmail = User::where('email', $email)->first();
        $token = $data->token;
        if($save){
        $res = ([
            'message'=> 'Success sign up, check email for verification',
            'link'=> 'http://localhost:8000/user/activation?token='.$token,
            'data'=> $data
        ]);
            return response()->json($res, 201);
        }else{
            $message = "Parameter is Invalid";
            return response()->json($message, 400);
        }
    }

    public function activate(Request $request)
    {
        $token = $request->token;

        $statusCheck = DB::table('users')->where('token', $token)->pluck('status')->first();;
        if($statusCheck === false){
            $update = DB::table('users')->select('*')->where('token', $token)->update(['status' => 1]);
            $res = ([
                'message'=> 'Your account has been activated',
            ]);
            return response()->json($res, 200);
            // return response()->$data;
        }else{
            $message = "Your account has already activated";
            return response()->json($message, 400);
        }
    }

    // public function showAll()
    // {
    //     $data = User::all();
    //     $response=
    //         // 'data'=> $data
    //         $data
    //     ;
    //     return response($response);
    // }

    // public function showDetailData($id)
    // {
    //     $data = User::where('id', $id)->first();
    //     if($data){
    //         return response()->json($data, 200);
    //     }
    //     else{
    //         $message = "Not found";
    //         return response()->json($message, 404);
    //         }
    // }

    // public function update(Request $request, $id)
    // {
    //     $data = User::find($id);
    //     // dd($request->all());
    //     // return response($request);
    //     $update = $data->update([
    //         'name'=> $request->name,
    //         'unit'=> $request->unit,
    //     ]);

    //      if($update){
    //         $message = "Success edit User";
    //         return response()->json($message, 200);
    //     }else{
    //         $message = "Empty Request Body";
    //         return response()->json($message, 400);
    //     }
    //     return response($response);
    // }

    // public function delete($id)
    // {
    //     $data = User::find($id);
    //     $delete = $data->delete();

    //     if($delete){
    //         $message = "Success delete User, id: $id";
    //         return response()->json($message, 200);
    //     }else{
    //         $message = "Parameter is Invalid";
    //         return response()->json($message, 404);
    //     }
    //     // return response($response);
    // }
    //
}
