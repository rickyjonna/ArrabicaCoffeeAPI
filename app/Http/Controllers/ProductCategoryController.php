<?php

namespace App\Http\Controllers;

use App\Product_category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //pake facades DB
use Validator, Input, Redirect;

class ProductCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware("login");
    }

    public function insertproductcategory(Request $request)
    {
        if ($request->isMethod('post')) 
        {
            $validator = Validator::make($request->all(), [
                'information' => 'required||unique:product_category'         
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
                $information = $request->input('information'); 

                //making Table
                $data = [
                    'information' => $information
                ];
                $insert = Product_category::create($data);
                DB::commit();
                $out  = [
                    "message" => "Produk Kategori Telah Dibuat  ",
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

    public function index()
    {
        $getPost = Product_category::OrderBy("id", "ASC")
        ->pluck('information');

        $out = [
            "message" => "List Kategori Produk",
            "results" => $getPost,
            "code" => 200
        ];
        return response()->json($out, $out['code']);
    }

    public function updateproductcategory($id, Request $request)
    {
        if ($request->isMethod('patch')) 
        {
            $validator = Validator::make($request->all(), [
                'information' => 'required'
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
                $information = $request->input('information');

                //updating old product_category
                $oldproductcategory = Product_category::where('id','=',$id);
                $data = [
                    'information' => $information
                ];
                $oldproductcategory -> update($data);
                DB::commit();
                $out  = [
                    "message" => "Product Kategori Telah Diperbaharui",
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

    public function destroy($id)
    {
        $product_category =  Product_category::where('id','=',$id)->first();

        if (!$product_category) {
            $data = [
                "message" => "error / data not found",
                "code" => 404
            ];
        } else {
            $product_category->delete();
            $data = [
                "message" => "success deleted",
                "code" => 200
            ];
        };
        return response()->json($data, $data['code']);
    }
}