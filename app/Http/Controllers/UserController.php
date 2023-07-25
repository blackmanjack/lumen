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
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Token;


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
            'username' => 'required|unique:user_person',
            'email' => 'required|email|unique:user_person',
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
        $data->password = hash('sha256',$request->password);
        $data->email = $request->email;
        $username = $data->username;
        $password = $request->password;
        

        if($data->email === '' || $data->password === '' || $data->username === ''){
            $message = 'Parameter mustn\'t empty';
            return response()->json($message, 400);
        }
        $save = $data->save();

        $payload = [
            "username" => $data["username"],
            "id_user" => $data["id_user"],
            "isadmin" => 0
        ];
        
        $token = JWTAuth::customClaims($payload)->fromUser($data);

        $url = env('APP_URL') . ':' . env('APP_PORT') . '/user/activation?token=' . $token;

        if($save){
            $res = ([
                'message'=> 'Success sign up, check email for verification',
                'data'=> $data
            ]);
            $mailData = [
                'id_user' => $data->id_user,
                'username' => $data->username,
                'link'=> $url

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
        $jwtToken = new Token($token);
        $decodedToken = JWTAuth::manager()->decode($jwtToken);
    
        // Access the token claims
        $claims = $decodedToken->getClaims();
        $username = $claims['username']->getValue();

        $findObj = DB::table('user_person')->where('username', $username)
                                    ->pluck('username')
                                    ->first();

        if($findObj) {
            $statusCheck = DB::table('user_person')->where('username', $username)
                                            ->pluck('status')
                                            ->first();
            if($statusCheck === false){
                $update = DB::table('user_person')->select('*')
                                            ->where('username', $username)
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
        
        $credentials = request(['username', 'password']);

        $statusCheck = DB::table('user_person')->where('username', $credentials["username"])
                                        ->pluck('status')
                                        ->first();

        $hashpasswd = hash('sha256', $credentials["password"]);

        $userCheck = User::where('username', $credentials["username"])
                    ->where('password', $hashpasswd)
                    ->first();
                                                    
        if (!$userCheck) {
            $res = ([
                'message'=> 'Username or password doesn\'t match our credentials!',
            ]);
            return response()->json($res, 404);
        }

        if(!$statusCheck){
            $res = ([
                'message'=> 'Please activate your account, check your email',
            ]);
            return response()->json($res, 400);
        } else {
            $user = User::where('username', $credentials["username"])->first();
            
            $isAdmin = User::where('username', $credentials["username"])
                                        ->pluck('isadmin')
                                        ->first();
            $payload = [
                "username" => $user["username"],
                "id_user" => $user["id_user"],
                "isadmin" => $isAdmin
            ];
            
            $newtoken = JWTAuth::customClaims($payload)->fromUser($user);
            $res = ([
                'message'=> 'Login Succesfullly',
                'token' => $newtoken,
            ]);
            return response()->json($res, 200);
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

        if($username !== '' || $email !== ''){
            if($username === $userFind){
                //update random passwd and send to email's user
                $newpasswd = Str::random(10);
                $emailFind->password = hash('sha256',$newpasswd);
                
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
        $userid = Auth::id();
        if(intval($id) !== $userid){
            $message = "Can't edit another user's account";
            return response()->json($message, 403);
        }

        //only accept headers application/x-www-form-urlencoded & application/json
        $contentType = $request->headers->get('Content-Type');
        $split = explode(';', $contentType)[0];
        if($split !== "application/x-www-form-urlencoded" && $split !== "application/json"){
            $message = "Content-Type ".$split." Not Support, only accept application/x-www-form-urlencoded & application/json";
            return response()->json($message, 415);
        }
        
        $this->validate($request, [
            'oldpassword' => 'required',
            'newpassword' => 'required|min:8'
        ]);

        $oldpasswd = $request->oldpassword;
        $newpasswd = $request->newpassword;

        $data = User::where('id_user', $userid)->first();
        $username = DB::table('user_person')->where('id_user', $userid)->pluck('username')->first();
        $hashpasswd = DB::table('user_person')->where('id_user', $userid)->pluck('password')->first();
        $isValidPassword = hash('sha256',$oldpasswd) === $hashpasswd;
        
        if($isValidPassword){
            $data->password = hash('sha256',$newpasswd);

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
        if(intval($id) !== $userid){
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
