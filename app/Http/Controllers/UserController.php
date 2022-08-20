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
        // $token = Str::random(32);
        $findObj = DB::table('users')->where('token', $token)->pluck('token')->first();
        // dd($findObj);
        if($findObj) {
            $statusCheck = DB::table('users')->where('token', $token)->pluck('status')->first();
            if($statusCheck === false){
                $update = DB::table('users')->select('*')->where('token', $token)->update(['status' => 1]);
                
                $message = 'Your account has been activated';
                return response()->json($res, 200);
            }else{
                $message = 'Your account has already activated';
                return response()->json($message, 400);
            }
        }else {
            $res = ([
                'message'=> 'Token Not Found',
            ]);
            return response()->json($res, 404);
        }
        
    }

    public function login(Request $request)
    {
        // $data = User::all();
        $data->username = $request->username;
        $data->password = $request->password;
        
        $response=
            // 'data'=> $data
            $data
        ;
        return response($response);
    }

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

    public function update(Request $request, $id)
    {
        // $data = Sensor::find($id);
        // // dd($request->all());
        // // return response($request);
        // $update = $data->update([
        //     'name'=> $request->name,
        //     'unit'=> $request->unit,
        // ]);
        $oldpasswd = $request->oldpassword;
        $newpasswd = $request->newpassword;

        // dd($newpasswd);

        //  if($update){
        //     $message = "Success edit Sensor";
        //     return response()->json($message, 200);

        $data = User::find($id);
        // return response($request);
        $passwdCheck = DB::table('users')->where('id', $id)->pluck('password')->first();


        // // dd($passwdCheck == $newpasswd);
        if($oldpasswd !== '' || $newpasswd !== ''){
            // dd($passwdCheck === $oldpasswd && $newpasswd !== '');
            if($passwdCheck === $oldpasswd && $newpasswd !== ''){
                $data->password = $newpasswd;
                $update = $data->save();

                if($update){
                    $res = ([
                        'message' => "Success change password",
                        'data' => $data
                    ]);
                    return response()->json($res, 200);
                }
                //unused code
                else{
                    $message = "Fail change password";
                    return response()->json($message, 400);
                }
            }else if($oldpasswd !== $passwdCheck){
                $message = "Old password is Invalid";
                return response()->json($message, 400);
            }else if($newpasswd === ''){
                $message = "Missing Parameter newpassword";
                return response()->json($message, 400);
            }
        // dd($passwdCheck === $oldpasswd);
        }else{
            $message = "Empty body request";
            return response()->json($message, 400);
        }

    }

    public function delete($id)
    {
        $data = User::find($id);
        $delete = $data->delete();

        if($delete){
            $message = "Success delete User, id: $id";
            return response()->json($message, 200);
        }else{
            $message = "Parameter is Invalid";
            return response()->json($message, 404);
        }
    }
    
}
