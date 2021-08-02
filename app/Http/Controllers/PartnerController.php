<?php

namespace App\Http\Controllers;

use App\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //pake facades DB
use Validator, Input, Redirect;

class PartnerController extends Controller
{
    public function __construct()
    {
        $this->middleware("login");
    }

    public function insertpartner(Request $request)
    {
        if ($request->isMethod('post')) 
        {
            $validator = Validator::make($request->all(), 
            [
                'owner' => 'required|unique:partners|max:30',
                'profit' => 'required|max:4'                
            ]);
            $messages = $validator->errors();
            if ($validator->fails()) 
            {
                $out = [
                    "message" => $messages->first(),
                    "code"   => 400
                ];
                return response()->json($out, $out['code']);
            };

            DB::beginTransaction();
            try {
                //initialize
                $owner = $request->input('owner');
                $profit = $request->input('profit');       //janganlupa  ubah  profit ke bentuk non %    

                //making partner
                $data = [
                    'owner' => $owner,
                    'profit' => $profit         
                ];
                $insert = Partner::create($data);
                DB::commit();
                $out  = [
                    "message" => "Rekan Telah Dibuat",
                    "code"  => 200
                ];
                return response()->json($out, $out['code']);
            } catch (\exception $e) {
                DB::rollback();
                $errorcode = $e->getcode();
                $message = $e->getmessage();
                if ($e instanceof \PDOException){ //dikarenakan kalau ada inputan empty mengakibatkan $e tidak catch apa2(kosong) maka dibuat code ini << belum
                    $out = [
                        "message" => $message,
                        "code" => 400
                        ];
                } else {
                    $out = [
                        "message" => "Error, Ada Inputan Kosong",
                        "code" => 400
                    ];
                };
                return response()->json($out,$out['code']);
            };
        }
    }

    public function index()
    {
        $getPost = Partner::Select('owner as Rekan', 'profit as Profit') 
        ->OrderBy("id", "DESC")
        ->get();

        $out = [
            "message" => "List Rekan",
            "results" => $getPost,
            "code" => 200
        ];
        return response()->json($out, $out['code']);
    }

    public function updatepartner($id, Request $request)
    {
        if ($request->isMethod('patch')) 
        {
            $validator = Validator::make($request->all(), 
            [
                'owner' => 'required|max:30',
                'profit' => 'required|max:4' 
            ]);
            $messages = $validator->errors();
            if ($validator->fails()) {
                $out = [
                    "message" => $messages->first(),
                    "code"   => 400
                ];
            return response()->json($out, $out['code']);
            };

            DB::beginTransaction();
            try {
                //initialize
                $owner = $request->input('owner');
                $profit = $request->input('profit'); 

                //updating old partner
                $oldpartner = Partner::where('id','=',$id);
                $data = [
                    'owner' => $owner,
                    'profit' => $profit
                ];
                $updatepartner = $oldpartner -> update($data);
                DB::commit();
                $out  = [
                    "message" => "Rekan Berhasil Diperbaharui",
                    "code"  => 200
                ];
                return response()->json($out, $out['code']);
            }catch(\exception $e) {
                DB::rollback();
                $errorcode = $e->getcode();
                $message = $e->getmessage();
                if ($e instanceof \PDOException){ //dikarenakan kalau ada inputan empty mengakibatkan $e tidak catch apa2(kosong) maka dibuat code ini << belum
                    
                    $out = [
                        "message" => $message,
                        "code" => 400
                    ];
                } else {
                    $out = [
                        "message" => "Error, Ada Inputan Kosong",
                        "code" => 400
                    ];
                };
                return response()->json($out,$out['code']);
            };        
        };
    }

    public function destroy($id)
    {
        $partner =  Partner::where('id','=',$id)->first();

        if (!$partner) {
            $data = [
                "message" => "error / data not found",
                "code" => 404
            ];
        } else {
            $partner->delete();
            $data = [
                "message" => "success deleted",
                "code" => 200
            ];
        };
        return response()->json($data, $data['code']);
    }

}