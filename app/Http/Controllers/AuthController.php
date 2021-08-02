<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Validator, Input, Redirect;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'user_type_id' => 'bail|required|max:11',
                'phone_number' => 'bail|required|unique:users|max:255',
                'password' => 'bail|required|min:6',
                'name' => 'bail|required|max:255',
                'address' => 'bail|required|max:255'
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
            $user_type_id = $request->input("user_type_id");
            $phone_number = $request->input("phone_number");
            $password = $request->input("password");
            $name = $request->input("name");
            $address = $request->input("address");
            $hashPwd = Hash::make($password);
            $data = [
                "user_type_id" => $user_type_id,
                "phone_number" => $phone_number,
                "password" => $hashPwd,
                "name" => $name,
                "address" => $address
            ];
            User::create($data);
            $out = [
                "message" => "Akun Berhasil Dibuat",
                "code"    => 200
            ];
            return response()->json($out, $out['code']);

        } catch (\exception $e){
            $errorcode = $e->getcode();
            $errormessage = $e->getmessage();
            $errormessage = substr($errormessage, 22, 75); // harusnya lebih dinamis << karena erornya sekarang cuma buat mati lamput jadi gpp
            $out = [
                "message" => $errorcode.$errormessage,
                "code"    => 400
            ];
            return response()->json($out, $out['code']); 
        };
    }

    public function login(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required',
                'password' => 'required|min:6'
            ]);
            $messages = $validator->errors();
            if ($validator->fails()) {
                $out = [
                    "message" => $messages->first(),
                    "code"   => 400
                ];
                return response()->json($out, $out['code']);
            };

            $phone_number = $request->input("phone_number");
            $password = $request->input("password");

            $user = User::where("phone_number","=", $phone_number)->first();

            if (!$user) {
                $out = [
                    "message" => "User Tidak ditemukan",
                    "code"    => 404,
                    "result"  => [
                        "token" => null,
                    ]
                ];
                return response()->json($out, $out['code']);
            }

            if (Hash::check($password, $user->password)) {
                $newtoken  = $this->generateRandomString();

                $user->update([
                    'token' => $newtoken
                ]);

                $out = [
                    "message" => "Login Berhasil",
                    "code"    => 200,
                    "result"  => [
                        "token" => $newtoken,
                    ]
                ];
            } else {
                $out = [
                    "message" => "Password Salah",
                    "code"    => 400,
                    "result"  => [
                        "token" => null,
                    ]
                ];
            };
            return response()->json($out, $out['code']);
        } catch (\exception $e){
            $errorcode = $e->getcode();
            $errormessage = $e->getmessage();
            $errormessage = substr($errormessage, 22, 75); // harusnya lebih dinamis << karena erornya sekarang cuma buat mati lamput jadi gpp
            $out = [
                "message" => $errorcode.$errormessage,
                "code"    => 400
            ];
            return response()->json($out, $out['code']); 
        };
    }

    function generateRandomString($length = 80)
    {
        $karakter = '012345678dssd9abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $panjang_karakter = strlen($karakter);
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $karakter[rand(0, $panjang_karakter - 1)];
        }
        return $str;
    }
}