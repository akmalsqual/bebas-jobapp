<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends BaseController
{
    /**
     * Register API
     * This is for registering new user to sistem
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required',
            'email'      => 'required|email',
            'password'   => 'required',
            'c_password' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Sorry, some data is not valid', $validator->errors());
        }

        $inputData             = $request->all();
        $inputData['password'] = bcrypt($inputData['password']);
        $user                  = User::create($inputData);
        $success['token']      = $user->createToken('theApp')->accessToken;
        $success['name']       = $user->name;

        return $this->sendResponse($success, 'User was successfully registered');
    }

    /**
     * Login API
     * This is for login to sistem
     * @route /login POST
     *
     * @param Request $request - [email, password]
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $email    = $request->get('email');
        $password = $request->get('password');

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = Auth::user();
            $success['token']      = $user->createToken('theApp')->accessToken;
            $success['name']       = $user->name;

            return $this->sendResponse($success, 'User successfully login');
        } else {
            return $this->sendError('Unauthorized', ['error' => 'Unauthorized']);
        }
    }


}
