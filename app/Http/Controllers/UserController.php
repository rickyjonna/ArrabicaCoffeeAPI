<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Validator, Input, Redirect;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware("login");
    }

    public function index()
    {
        $getListUser = User::select('name','phone_number','information')
        ->OrderBy("users.id", "DESC")
        ->leftjoin('user_type', 'user_type.id', '=', 'users.user_type_id')
        ->get();

        $out = [
            "message" => "List User",
            "results" => $getListUser
        ];
        return response()->json($out, 200);        
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), 
        [
            'user_type_id' => 'required',
            'phone_number' => 'required',
            'name' => 'required',
            'address' => 'required'
        ]);
        $messages = $validator->errors();
        if ($validator->fails()) {
            $out = [
                "message" => $messages->first(),
                "code"   => 400
            ];
        return response()->json($out, $out['code']);
        };   
        //initialize
        $user_type_id = $request->input('user_type_id');
        $phone_number = $request->input('phone_number');
        $name = $request->input('name');   
        $address = $request->input('address');   
        //updating 
        $olduser = User::where('id','=',$id)->first();
        if(!$olduser){
            $out  = [
                "message" => "User Tidak ditemukan",
                "code"   => 404                       
            ];
            return response()->json($out, $out['code']);
        };
        $newdatauser = [
            'user_type_id' => $user_type_id,
            'phone_number' => $phone_number,             
            'name' => $name,
            'address' => $address      
        ];
        $update_user = $olduser -> update($newdatauser);
        if ($update_user) 
        {
            $out  = [
                "message" => "Update User Berhasil",
                "code"  => 200,
                "results" => $newdatauser                        
            ];
            return response()->json($out, $out['code']);
        };     
    }

    public function destroy($id)
    {
        $user =  User::where('id','=',$id)->first();
        if (!$user) {
            $data = [
                "message" => "User Tidak Ditemukan",
                "code" => 404
            ];
        } else {
            $user->delete();
            $data = [
                "message" => "User Berhasil Dihapus",
                "code" => 200
            ];
        };
        return response()->json($data, $data['code']);
    }
}