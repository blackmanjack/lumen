<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        $this->validate($request, [
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8'
        ],
        [   
            'username.required' => 'Parameter username mustn\'t empty',
            'email.unique'      => 'Sorry, This Email Address Is Already Used By Another User. Please Try With Different One, Thank You.',
            'password.required' => 'Password Is Required For Your Information Safety, Thank You.',
            'password.min'      => 'Password Length Should Be at Least 8 Character Or Digit Or Mix, Thank You.',
        ]);

        $data = new User();
        $data->username = $request->username;
        $data->password = Hash::make($request->password);
        $data->email = $request->email;
        $data->token = Str::random(32);
        // $data->status = 0;
        // $data->is_admin = 0;

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
                return response()->json($message, 200);
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
        $username = $request->input('username');
        $password = $request->input('password');

        $user = User::where('username', $username)->first();
        $hashpasswd = DB::table('users')->where('username', $username)->pluck('password')->first();
        $statusCheck = DB::table('users')->where('username', $username)->pluck('status')->first();
        $passwdCheck = Hash::check($password, $hashpasswd);
        // dd($user);
        if($user){
            if($statusCheck && $passwdCheck){
                $update = DB::table('users')->select('*')->where('username', $username)->update(['token' => base64_encode(Str::random(32))]);
                $user1 = User::where('username', $username)->first();
                $res = ([
                    'message'=> 'Login Succesfullly',
                    'data' => $user1
                ]);
                return response()->json($res, 200);
            }else if($password !== $passwdCheck){
                $res = ([
                    'message'=> 'Wrong Password',
                ]);
                return response()->json($res, 400);
            }else{
                $res = ([
                    'message'=> 'Please activate your account, check your email',
                ]);
                return response()->json($res, 400);
            }
        }else{
            $res = ([
                'message'=> 'User Not Found, Wrong Username',
            ]);
            return response()->json($res, 404);
        }
       
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

    public function resetpasswd(Request $request)
    {
        $username = $request->username;
        $email = $request->email;

        $emailFind = User::where('email', $email)->first();
        $userFind = User::where('email', $email)->pluck('username')->first();


        // $passwdCheck = DB::table('users')->where('id', $id)->pluck('password')->first();
        if($username !== '' || $email !== ''){
            if($username === $userFind){
                //update random passwd and send to email's user
                $newpasswd = Str::random(10);
                $emailFind->password = Hash::make($newpasswd);
                $update = $emailFind->save();

                $res = ([
                    'message'=> "New password request sent. Check email for new password",
                    'data' => $emailFind,
                    'newpasswd' => $newpasswd
                ]);
                return response()->json($res, 200);
            }else{
                $res = ([
                    'message'=> "Username or email is incorrect",
                ]);
                return response()->json($res, 400);
            }

            // if($passwdCheck === $username && $email !== ''){
            //     $data->password = $email;
            //     $update = $data->save();

            //     if($update){
            //         $res = ([
            //             'message' => "Success change password",
            //             'data' => $data
            //         ]);
            //         return response()->json($res, 200);
            //     }
            //     //unused code
            //     else{
            //         $message = "Fail change password";
            //         return response()->json($message, 400);
            //     }
            // }else if($username !== $passwdCheck){
            //     $message = "Old password is Invalid";
            //     return response()->json($message, 400);
            // }else if($email === ''){
            //     $message = "Missing Parameter newpassword";
            //     return response()->json($message, 400);
            // }
        }else{
            $message = "Empty body request";
            return response()->json($message, 400);
        }

    }

    public function update(Request $request, $id)
    {
        $oldpasswd = $request->oldpassword;
        $newpasswd = $request->newpassword;

        $data = User::find($id);
        $hashpasswd = DB::table('users')->where('id', $id)->pluck('password')->first();
        $passwdCheck = Hash::check($oldpasswd, $hashpasswd);
        if($oldpasswd !== '' || $newpasswd !== ''){
            if($passwdCheck === $oldpasswd && $newpasswd !== ''){
                $data->password = Hash::make($newpasswd);
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
            }else if($passwdCheck ===  false){
                $message = "Old password is Invalid";
                return response()->json($message, 400);
            }else if($newpasswd === ''){
                $message = "Missing Parameter newpassword";
                return response()->json($message, 400);
            }
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
