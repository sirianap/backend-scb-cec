<?php
 
    namespace App\Http\Controllers;
 
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use App\User;
    use Illuminate\Support\Facades\Auth;
    use Validator;
 
    class UserController extends Controller
    {
 
        public $successStatus = 200;

        public function index()
        {
            $users = User::all();
            return response()->json($users, 200);
        }
 
        public function login(){
            if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
                $user = Auth::user();
                $success['token'] =  $user->createToken('nApp')->accessToken;
                return response()->json($success, $this->successStatus);
            }
            else{
                return response()->json(['error'=>'Eitss ada yang salah nih'], 401);
            }
        }
 
        public function register(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required',
                'password' => 'required',
            ]);
 
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            }
 
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $success['token'] =  $user->createToken('nApp')->accessToken;
            $success['name'] =  $user->name;
 
            return response()->json(['success'=>$success], $this->successStatus);
        }
 
        public function details()
        {
            $user = Auth::user();
            return response()->json(['user' => $user], $this->successStatus);
        }
        public function update(User $user,Request $request)
        {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required',
                'password' => 'required',
            ]);
 
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);            
            }
 
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user->name = $input['name'];
            $user->email = $input['email'];
            $user->role = $input['role'];
            if($input['role']){
                $user->password = $input['password'];
            }
            $user->save();
            return response()->json(['message'=>'Data berhasil di update'], 200 );
        }
        
        public function logout(){ 
            if (Auth::check()) {
                Auth::user()->AauthAcessToken()->delete();
            }
        }
    }