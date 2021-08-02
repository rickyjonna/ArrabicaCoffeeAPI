<?php

use App\Product;
use App\Product_stock;
use App\Product_formula;
use App\Product_price_vendor;
use Illuminate\Support\Facades;


if(!function_exists('make_product')) {
    function make_product($request)
    {   
            $merchant_id = $request->input('merchant_id');
            $partner_id = $request->input('partner_id');
            $product_category_id = $request->input('product_category_id');
            $name = $request->input('name');
            $price = $request->input('price');
            $discount = $request->input('discount');
            $isformula = $request->input('isformula');
            $hasstock = $request->input('hasstock');
            $information = $request->input('information');        
            $dataproduct = [
                'merchant_id' => $merchant_id,
                'partner_id' => $partner_id,
                'product_category_id' => $product_category_id,
                'name' => $name,
                'price' => $price,
                'discount' => $discount,
                'isformula' => $isformula,
                'hasstock' => $hasstock,
                'information' => $information
            ];
            Product::create($dataproduct);
            return ($dataproduct);
    }
};

if(!function_exists('make_stock')) {
    function make_stock($request,$id)
    {   
        $merchant_id = $request->input('merchant_id');
        $amount = $request->input('amount');
        $minimum_amount = $request->input('minimum_amount');         
        $datastock = [
            'merchant_id' => $merchant_id,
            'product_id' => $id,
            'amount' => $amount,
            'minimum_amount' => $minimum_amount
        ];
        Product_stock::create($datastock);
        return ($datastock);
    }
};

if(!function_exists('make_formula')) {
    function make_formula($request,$id)
    {   
        $merchant_id = $request->input('merchant_id');
        $ingredient = $request->input('ingredient');        
        $total_ingredientarray = count($ingredient) / 2;
        for ($i=0; $i<$total_ingredientarray; $i++)
        {
            $dataformula =[
                'merchant_id' => $merchant_id,
                'product_id' => $id,
                'ingredient_id' => $ingredient[0],
                'amount' => $ingredient[1]
                ];
            Product_formula::create($dataformula); 
            $ingredient = array_splice($ingredient,2);
        };
        return ($dataformula);
    }
};

if(!function_exists('make_vendorprice')) {
    function make_vendorprice($request,$id)
    {   
        $merchant_id = $request->input('merchant_id');
        $vendor_price = $request->input('vendor_price');
        $total_vendorpricearray = count($vendor_price) / 2;
        for ($i=0; $i<$total_vendorpricearray; $i++) {
            $datavendorprice = [
                'merchant_id' => $merchant_id,
                'product_id' => $id,
                'vendor_id' => $vendor_price[0],
                'vendor_price' => $vendor_price[1]
            ];
            Product_price_vendor::create($datavendorprice);
            $vendor_price = array_splice($vendor_price, 2);
        };
    }
};

if(!function_exists('update_product')) {
    function update_product($request, $id)
    {   
            $merchant_id = $request->input('merchant_id');
            $partner_id = $request->input('partner_id');
            $product_category_id = $request->input('product_category_id');
            $name = $request->input('name');
            $price = $request->input('price');
            $discount = $request->input('discount');
            $isformula = $request->input('isformula');
            $hasstock = $request->input('hasstock');
            $information = $request->input('information');        
            $dataproduct = [
                'merchant_id' => $merchant_id,
                'partner_id' => $partner_id,
                'product_category_id' => $product_category_id,
                'name' => $name,
                'price' => $price,
                'discount' => $discount,
                'isformula' => $isformula,
                'hasstock' => $hasstock,
                'information' => $information
            ];
            Product::where('id','=',$id)->update($dataproduct);
            return ($dataproduct);
    }
};

if(!function_exists('update_stock')) {
    function update_stock($request, $idlama, $idbaru)
    {   
        $merchant_id = $request->input('merchant_id');
        $amount = $request->input('amount');
        $minimum_amount = $request->input('minimum_amount');
        $datastock = [
            'merchant_id' => $merchant_id,
            'product_id' => $idbaru,
            'amount' => $amount,
            'minimum_amount' => $minimum_amount
        ];
        $update_stock = Product_stock::where('product_id', '=', $idlama)->update($datastock);
        if($update_stock){
            
        } else {
            $make_stock = Product_stock::create($datastock);
        };
    }
};

if(!function_exists('update_formula')) {
    function update_formula($request, $idlama, $idbaru)
    {   
        //delete all of the old formula
        Product_formula::where('product_id','=',$idlama)->delete(); 
        //creating new formula with looping method (from input of ingredient array)
        $merchant_id = $request->input('merchant_id');
        $ingredient = $request->input('ingredient');
        $total_ingredientarray = count($ingredient) / 2;     
        for ($i=0; $i<$total_ingredientarray; $i++)
        {
            $dataformula =[
                'merchant_id' => $merchant_id,
                'product_id' => $idbaru,
                'ingredient_id' => $ingredient[0],
                'amount' => $ingredient[1]
                ];
            $update_formula = Product_formula::create($dataformula);
            $ingredient = array_splice($ingredient,2);
        };
    }
};