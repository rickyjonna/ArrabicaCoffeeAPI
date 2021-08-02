<?php

namespace App\Http\Controllers;

use App\Order;
use App\Order_list;
use App\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //pake facades DB
use Validator, Input, Redirect;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware("login");
    }

    public function insertorder(Request $request)
    {
        if ($request->isMethod('post')) 
        {
            $validator = Validator::make($request->all(), 
            [
                'merchant_id' => 'required|integer',
                'table_id' => 'nullable|integer',
                'user_id' => 'required|integer', // id pemesan
                'vendor_id' => 'nullable|integer',
                'information' => 'nullable', 
                'product_id' => 'required|array',           
                'amount' => 'required|array',
                'discount' => 'required'
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
            try{
                //initialize
                $merchant_id = $request->input('merchant_id');
                $table_id = $request->input('table_id');         
                $user_id = $request->input('user_id'); 
                $vendor_id = $request->input('vendor_id');  
                $information = $request->input('information');  
                $product_id = $request->input('product_id');
                $product_id_count = count($product_id); 
                $amount = $request->input('amount'); 
                $discount = $request->input('discount'); 

                //making Order
                $data = [
                    'merchant_id' => $merchant_id,
                    'table_id' => $table_id,
                    'user_id' => $user_id,
                    'vendor_id' => $vendor_id,
                    'status' => "OPEN",
                    'information' => $information
                ];
                $insert = Order::create($data);

                //get the order_id
                $order_id = Order::max('id');

                //making Order list
                for($i=0; $i < $product_id_count; $i++) 
                {
                    $data = [
                        'order_id' => $order_id,
                        'merchant_id' => $merchant_id,
                        'product_id' => $product_id[$i],
                        'user_id' => $user_id,
                        'order_list_status_id' => 1,
                        'amount' => $amount[$i],
                        'discount' => $discount
                    ];
                    $insert = Order_list::create($data);
                };
                //a

                DB::commit();
                $out  = [
                    "message" => "Order Telah Dibuat",
                    "code" => 200
                ];               
                return response()->json($out,$out['code']);

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

    public function index()
    {
        $Meja = Order::leftjoin('tables', 'tables.id', '=', 'orders.table_id')
        ->where('orders.status', "=", "OPEN")
        ->where('orders.table_id','!=',null)
        ->OrderBy("orders.id", "ASC")
        ->pluck('tables.number');

        // masih manual coding
        $gojek = Order::leftjoin('vendors','vendors.id','=','orders.vendor_id') 
        ->addselect('information')
        ->where('orders.status', "=", "OPEN")
        ->where('orders.vendor_id','!=',10)
        ->orderby('orders.id',"ASC")
        ->pluck('information');

        $grab = Order::leftjoin('vendors','vendors.id','=','orders.vendor_id') 
        ->addselect('information')
        ->where('orders.status', "=", "OPEN")
        ->where('orders.vendor_id','!=',9)
        ->orderby('orders.id',"ASC")
        ->pluck('information');

        $out = [
            "message" => "List Order",
            "results" => "Meja : $Meja" . "Gojek : $gojek" . "Grab : $grab", // flatten nanti
            "code" => 200
        ];
        return response()->json($out, $out['code']);
    }
}