<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Node;
use App\Models\Hardware;
use App\Models\Sensor;
use App\Mail\PasswdEmail;
use App\Mail\VerifyEmail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;


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
        $username = $data->username;
        $password = $request->password;
        $data->token = base64_encode($username.':'.$password);

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
                'data'=> $data
            ]);
            $mailData = [
                'id_user' => $data->id,
                'username' => $data->username,
                'link'=> 'http://localhost:8000/user/activation?token='.$token,
            ];
            
            Mail::to($data->email)->send(new VerifyEmail($mailData));

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
        $findObj = DB::table('users')->where('token', $token)
                                    ->pluck('token')
                                    ->first();

        if($findObj) {
            $statusCheck = DB::table('users')->where('token', $token)
                                            ->pluck('status')
                                            ->first();
            if($statusCheck === false){
                $update = DB::table('users')->select('*')
                                            ->where('token', $token)
                                            ->update(['status' => 1]);
                
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
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ],
        [   
            'username.required' => 'Parameter username mustn\'t empty',
            'password.required' => 'Password Is Required',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        $user = User::where('username', $username)->first();
        $hashpasswd = DB::table('users')->where('username', $username)
                                        ->pluck('password')
                                        ->first();
        $statusCheck = DB::table('users')->where('username', $username)
                                        ->pluck('status')
                                        ->first();
        $passwdCheck = Hash::check($password, $hashpasswd);

        if($user){
            if($statusCheck && $passwdCheck){
                $update = DB::table('users')->select('*')
                                            ->where('username', $username)
                                            // ->update(['token' => base64_encode(Str::random(32))]);
                                            ->update(['token' => base64_encode($username.':'.$password)]);
                $user1 = User::where('username', $username)->first();
                $api_token = User::where('username', $username)->pluck('token')->first();
                $res = ([
                    'message'=> 'Login Succesfullly',
                    'data' => $user1,
                    'api_token' => $api_token,
                ]);
                return response()->json($res, 200);
            }else if(!$passwdCheck){
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

    public function resetpasswd(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'email' => 'required'
        ]);
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
                ]);
                
                $mailData = [
                    'username' => $userFind,
                    'passwd' => 'This is your new password '.$newpasswd
                ];

                Mail::to($emailFind)->send(new PasswdEmail($mailData));
            
                return response()->json($res, 200);
            }else{
                $res = ([
                    'message'=> "Username or email is incorrect",
                ]);
                return response()->json($res, 400);
            }

        }else{
            $message = "Empty body request";
            return response()->json($message, 400);
        }

    }

    public function update(Request $request, $id)
    {
        //only accept headers application/x-www-form-urlencoded
        $contentType = $request->headers->get('Content-Type');
        $split = explode(';', $contentType)[0];
        if($split !== "application/x-www-form-urlencoded"){
            $message = "Supported format: application/x-www-form-urlencoded";
            return response()->json($message, 415);
        }
        $this->validate($request, [
            'oldpassword' => 'required',
            'newpassword' => 'required|min:8'
        ]);

        $userid = Auth::id();
        if($id !== $userid){
            $message = "Can't edit another user's account";
            return response()->json($message, 403);
        }

        $oldpasswd = $request->oldpassword;
        $newpasswd = $request->newpassword;

        $data = User::find($userid);
        $hashpasswd = DB::table('users')->where('id', $userid)->pluck('password')->first();
        $passwdCheck = Hash::check($oldpasswd, $hashpasswd);
            if($passwdCheck){
                $data->password = Hash::make($newpasswd);
                $update = $data->save();

                $res = ([
                    'message' => "Success change password",
                    'data' => $data
                ]);
                return response()->json($res, 200);
            }else{
                $message = "Old password is Invalid";
                return response()->json($message, 400);
            }
    }

    public function delete($id)
    {
        $userid = Auth::id();
        if($id !== $userid){
            $message = "Can\'t delete another user\'s account";
            return response()->json($message, 403);
        }

        $data = User::find($userid);
        
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
