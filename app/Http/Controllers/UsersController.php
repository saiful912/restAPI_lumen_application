<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index()
    {
        $user = app('db')->table('users')->get();
        return response()->json($user);
    }

    public function create(Request $request)
    {
        try {
            $this->validate($request, [
                'full_name' => 'required',
                'username' => 'required|min:4',
                'email' => 'required|email',
                'password' => 'required|min:6'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
        try {
            $id = app('db')->table('users')->insertGetId([
                'full_name' => trim($request->input('full_name')),
                'username' => trim($request->input('username')),
                'email' => strtolower($request->input('email')),
                'password' => app('hash')->make($request->input('password')),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $user = app('db')->table('users')->select('full_name', 'username', 'email')->where('id', $id)->first();

            return response()->json([
                'id' => $id,
                'full_name' => $user->full_name,
                'username' => $user->username,
                'email' => $user->email,
            ], 201);
        } catch (\PDOException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function authenticate(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required|min:6'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $token = app('auth')->attempt($request->only('email', 'password'));

        if ($token) {
            return response()->json([
                'success' => true,
                'message' => 'User Authenticate',
                'token' => $token
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Invalid Credentials',
        ], 400);
    }

    public function me()
    {
        $user = app('auth')->user();
        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'user profile found',
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'User not found',
        ]);
    }

}
