<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{

    public function index() {
        //
    }

    public function store(Request $request){

        $this->validate($request, [
            'name' => 'required|string|max:120',
            'bio' => 'required|string|max:120',
            'password' => 'required|string|min:6|max:10', 
            'email' => 'required|string|email|max:120|unique:users',
    	]);

        return User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'type' => $request['type'],
            'bio' => $request['bio'],
            'photo' => $request['photo'],
            'password' => Hash::make($request['password']),
        ]);
    }

    public function show($id){
        //
    }

    public function update(Request $request, $id){
        //
    }

    public function destroy($id){
        //
    }
}
