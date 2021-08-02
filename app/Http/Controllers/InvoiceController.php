<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\Order_list;
use App\Product;
use App\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //pake facades DB
use Validator, Input, Redirect;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware("login");
    }

    public function insertinvoice(Request $request)
    {
        if ($request->isMethod('post')) 
        {
            $validator = Validator::make($request->all(),  [
                'merchant_id' => 'required|integer',
                'order_id' => 'required|integer',
                'user_id' => 'required|integer',
                'payment_id' => 'required|integer',
                'discount' => 'required|max:4',
                'tax' => 'required|max:4',
                'phone_number' => 'nullable|max:20',
                "email" => 'nullable|max:20'
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
                $order_id = $request->input('order_id');         
                $user_id = $request->input('user_id'); 
                $payment_id = $request->input('payment_id');  
                $discount = $request->input('discount');  
                $tax = $request->input('tax'); 
                $phone_number = $request->input('phone_number');
                $email = $request->input('email');
                $totalprice = Order_list::where('order_list.order_id','=',$order_id) 
                ->leftjoin('products','products.id','=','order_list.product_id')
                ->sum(DB::raw('(products.price - (products.price * products.discount / 100)) * order_list.amount'));

                //making invoice
                $data = [
                    'merchant_id' => $merchant_id,
                    'order_id' => $order_id,
                    'user_id' => $user_id,
                    'payment_id' => $payment_id,
                    'discount' => $discount,
                    'tax' => $tax,
                    'total' => $totalprice,
                    'status' => "UNPAID",
                    'phone_number' => $phone_number,
                    'email' => $email
                ];
                $insert = Invoice::create($data);
                $invoicetotal = $totalprice - ($totalprice * $discount / 100) + ($totalprice * $tax / 100);
                DB::commit();
                $out  = [
                    "message" => "Invoice Berhasil Dibuat",
                    "code" => 200
                ];               
                return response()->json($out,$out['code']);
            } catch (\exception $e){
                DB::rollback();
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
    }//endfunc

    public function updateinvoice($id, Request $request)
    {
        if ($request->isMethod('patch')) 
        {
            $validator = Validator::make($request->all(), [
                'information' => 'nullable'                
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
                $data = [
                    'status' => "PAID"
                ];
                $invoice = Invoice::where('id','=',$id);
                $updateinvoice = $invoice->update($data);

                //buat laporan pemasukan
                $merchant_id = $invoice->max('merchant_id');
                $invoicetotal = $invoice->max('total');
                $invoicediscount = $invoice->max('discount');
                $invoicetax = $invoice->max('tax');
                $incometotal = $invoicetotal - ($invoicetotal * $invoicediscount / 100) + ($invoicetotal * $invoicetax / 100);
                $information = $request->input('information');
                $dataincome = [
                    'merchant_id' => $merchant_id,
                    'income_type_id' => 1,
                    'invoice_id' => $id,
                    'total' => $incometotal,
                    'information' => $information
                ];
                $income = Income::create($dataincome);
                DB::commit();
                if($income){
                    $out = [
                        'message' => 'success',
                        'code' => 200
                    ];
                }else{
                    $out = [
                        'message' => 'error',
                        'code' => 400
                    ];
                };
                return response() ->json($out,$out['code']);
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
        $invoice =  invoice::where('id','=',$id)->first();

        if (!$invoice) {
            $data = [
                "message" => "error / data not found",
                "data" => 404
            ];
        } else {
            $invoice->delete();
            $data = [
                "message" => "success deleted",
                "data" => 200
            ];
        };
        return response()->json($data, 200);
    }
}//endclass