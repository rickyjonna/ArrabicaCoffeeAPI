<?php

namespace App\Http\Controllers;

use App\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //pake facades DB
use Validator, Input, Redirect;

class TableController extends Controller
{
    public function __construct()
    {
        $this->middleware("login");
    }

    public function inserttable(Request $request)
    {
        if ($request->isMethod('post')) 
        {
            $validator = Validator::make($request->all(), [
                'merchant_id' => 'required|integer',
                'number' => 'required|integer',
                'extend' => 'required|integer'                
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
                $merchant_id = $request->input('merchant_id');
                $number = $request->input('number');      
                $extend = $request->input('extend'); 

                //making Table
                $data = [
                    'merchant_id' => $merchant_id,
                    'number' => $number,
                    'extend' => $extend
                ];
                Table::create($data);
                DB::commit();
                $out  = [
                    "message" => "Meja Telah Dibuat",
                    "results" => $data,
                    "code"  => 200
                ];
                return response()->json($out, $out['code']);
            }catch (\exception $e) {
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

    public function updatetable($id, Request $request)
    {
        if ($request->isMethod('patch')) 
        {
            $validator = Validator::make($request->all(), 
            [
                'merchant_id' => 'required|integer',
                'number' => 'required|integer',
                'extend' => 'required|integer'  
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
                $merchant_id = $request->input('merchant_id');
                $number = $request->input('number');
                $extend = $request->input('extend'); 


                //updating old Table
                $oldtable = Table::where('id','=',$id);
                $data = [
                    'number' => $number,
                    'extend' => $extend
                ];
                $updatetable = $oldtable -> update($data);
                DB::commit();
                $out  = [
                    "message" => "Meja Telah Diperbaharui",
                    "results" => $data,
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
        };
    }

    public function index()
    {
        $getPost = Table::Select('number as Nomor_Meja', 'extend as Extend') 
        ->OrderBy("id", "DESC")
        ->get();

        $out = [
            "message" => "List Meja",
            "results" => $getPost
        ];
        return response()->json($out, 200);
    }

    public function destroy($id)
    {
        $table =  Table::where('id','=',$id)->first();

        if (!$table) {
            $data = [
                "message" => "error / data not found",
                "code" => 404
            ];
        } else {
            $table->delete();
            $data = [
                "message" => "success deleted",
                "code" => 200
            ];
        };
        return response()->json($data, $data['code']);
    }
}