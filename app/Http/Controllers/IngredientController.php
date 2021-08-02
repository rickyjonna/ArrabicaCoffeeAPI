<?php

namespace App\Http\Controllers;

use App\Ingredient;
use App\Ingredient_stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator, Input, Redirect;

class IngredientController extends Controller
{
    public function __construct()
    {
        $this->middleware("login");
    }

    public function insertingredient(Request $request)
    {
        $validator = Validator::make($request->all(), 
            [
                'merchant_id' => 'required|integer',
                'name' => 'required|unique:ingredients|max:255',
                'unit' => 'required|max:255',
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
            //initialization
            $merchant_id = $request->input('merchant_id');
            $name = $request->input('name');
            $unit = $request->input('unit');
            $amount = $request->input('amount');
            //creating ingredient
            $data = [
                'merchant_id' => $merchant_id,
                'name' => $name,
                'unit' => $unit
            ];
            Ingredient::create($data);
            //get the ingredient id
            $ingredient_id = Ingredient::max('id');
            //creating ingredient stock
            $datastock = [
                'merchant_id' => $merchant_id,
                'ingredient_id' => $ingredient_id,
                'amount' => $amount
            ];
            Ingredient_stock::create($datastock);
            DB::commit();
            $out = [
                'message' => "Bahan Telah Dibuat",
                'code' => 200
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
    }

    public function index()
    {
        $getPost = Ingredient::leftjoin('ingredient_stock', 'ingredient_stock.ingredient_id', '=', 'ingredients.id')
        ->select('ingredients.id','name','amount', 'unit')
        ->OrderBy("ingredients.id", "DESC")
        ->get();

        $out = [
            "message" => "List Ingredient",
            "results" => $getPost,
            "code" => 200
        ];

        return response()->json($out, $out['code']);
    }

    public function updateingredient($id, Request $request)
    {
        if ($request->isMethod('patch')) 
        {
            $validator = Validator::make($request->all(), 
            [
                'merchant_id' => 'required|integer',
                'name' => 'required|max:255',
                'unit' => 'required|max:255',
                'amount' => 'required|integer'
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
                $merchant_id = $request->input('merchant_id');
                $name = $request->input('name');
                $unit = $request->input('unit');
                $amount = $request->input('amount');
                //updating ingredient
                $oldingredient = Ingredient::where("id","=",$id);
                $data = [
                    "merchant_id" => $merchant_id,
                    "name" => $name,
                    "unit" => $unit
                ];
                $updateingredient = $oldingredient -> update($data);
                //updating ingredient stock
                $oldingredientstock = Ingredient_stock::where("ingredient_id","=",$id);
                $datastock = [
                    "merchant_id" => $merchant_id,
                    "ingredient_id" => $id,
                    "amount" => $amount
                ];
                $updateingredientstock = $oldingredientstock -> update($datastock);
                DB::commit();
                $out  = [
                    "message" => "Bahan Telah Diperbaharui",
                    "code" => 200
                ];               
                return response()->json($out,$out['code']);
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
        $ingredient =  Ingredient::where('id','=',$id)->first();

        if (!$ingredient) {
            $data = [
                "message" => "error / data not found",
                "code" => 404
            ];
        } else {
            $ingredient->delete();
            $data = [
                "message" => "success deleted",
                "code" => 200
            ];
        };
        return response()->json($data, $data['code']);
    }
}
