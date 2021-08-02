<?php

namespace App\Http\Controllers;

use App\Order;
use App\Order_list;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //pake facades DB
use Validator, Input, Redirect;

class OrderListController extends Controller
{
    public function __construct()
    {
        $this->middleware("login");
    }

    public function index($id)
    {
        $order_list = Order_list::where('order_id','=',$id)->get();

        $out = [
            "message" => "List Pesanan",
            "results" => $order_list,
            "code" => 200
        ];
        return response()->json($out, $out['code']);
    }

    public function updateorderlist($id, Request $request)
    {
        if ($request->isMethod('patch')) 
        {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|integer'
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
                $amount = $request -> input('amount');
                $data = [
                    'amount' => $amount
                ];
                $update = Order_list::where('id','=',$id)->update($data);
                $deleteempty = Order_list::where('amount','=',0)->delete();
                DB::commit();
                $out = [
                    "message" => "Order List Berhasil Diperbaharui",
                    "code" => 200
                ];
                return response()->json($out,$out['code']);
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

    public function updateorderliststatus($id, Request $request)
    {
        if ($request->isMethod('patch')) 
        {
            $validator = Validator::make($request->all(), [
                'order_list_status_id' => 'required|integer'
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
                $order_list_status_id = $request -> input('order_list_status_id');
                $data = [
                    'order_list_status_id' => $order_list_status_id,
                ];
                $update = Order_list::where('id','=',$id)->update($data);
                //checking all orderlist in 1 order
                $orderlistorderid = Order_list::where('id','=',$id)->max('order_id');
                $orderlistnotdone = Order_list::where('order_id','=',$orderlistorderid)->where('order_list_status_id','!=',4)->max('id');
                if(!$orderlistnotdone){
                    $data = [
                        'status' => 'CLOSED'
                    ];
                    Order::where('id','=',$orderlistorderid)->update($data);
                };
                DB::commit();
                $out = [
                    "message" => "Order List Status Berhasil Diperbaharui",
                    "code" => 200
                ];
                return response()->json($out,$out['code']);
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

    public function destroy($id)
    {
        $deleteorderlist =  Order_list::where('id','=',$id)->first();

        if (!$deleteorderlist) {
            $data = [
                "message" => "error / data not found",
                "code" => 404
            ];
        } else {
            $deleteorderlist->delete();
            $data = [
                "message" => "success deleted",
                "code" => 200
            ];
        };
        return response()->json($data, $data['code']);
    }
}