<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\support\Facades\Auth;
use Illuminate\support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends BaseController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' =>'required|same:password',
        ]); 

        if ($validator->fails())
        {
            return $this->sendError('Please validate error', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('bosinanonanla')->accessToken;
        $success['name'] = $user->name;

        return $this->sendResponse($success, 'user registred successfully');
    }
    

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::User();
            $success['token'] = $user->createToken('bosinanonanla')->accessToken;
            $success['name'] = $user->name;

            return $this->sendResponse($success, 'user login successfully');
        } else {
            return $this->sendError('Please check your Auth', ['error' => 'Unauthorised']);
        }
    }
}
