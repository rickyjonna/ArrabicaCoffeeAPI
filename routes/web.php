<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () 
{
    return 
    "<h1>WarkopMarbun</h1>
    <p>     </p>
    <p>Login</p>
    <p>     </p>
    <p>register</p>";
});

//user
$router->post("/register", "AuthController@register");
$router->post("/login", "AuthController@login");
$router->get("/listuser", "UserController@index");
$router->patch("/updateuser/{id}", "UserController@update");
$router->delete("/deleteuser/{id}", "UserController@destroy");
//product
$router->post("/insertproduct", "ProductController@insertproduct");
$router->get("/listproduct", "ProductController@index");
$router->get("/listproduct/{categoryid}", "ProductController@indexbycategoryid");
$router->get("/listproductalert", "ProductController@indexalert");
$router->patch("/updateproduct/{id}", "ProductController@updateproduct");
$router->delete("/deleteproduct/{id}", "ProductController@destroy");
//ingredient
$router->get("/listingredient", "IngredientController@index");
$router->post("/insertingredient", "IngredientController@insertingredient");
$router->patch("/updateingredient/{id}", "IngredientController@updateingredient");
$router->delete("/deleteingredient/{id}", "IngredientController@destroy");
//stock
$router->get("/liststock", "ProductStockController@index");
$router->patch("/updatestock/{id}", "ProductStockController@updatestock");
$router->delete("/deletestock/{id}", "ProductStockController@destroy");
//formula
$router->get("/listformula", "ProductFormulaController@index");
$router->patch("/updateformula/{id}", "ProductFormulaController@updateformula");
$router->delete("/deleteformula/{id}", "ProductFormulaController@destroy");
//table
$router->post("/inserttable", "TableController@inserttable");
$router->get("/listtable", "TableController@index");
$router->patch("/updatetable/{id}", "TableController@updatetable");
$router->delete("/deletetable/{id}", "TableController@destroy");
//product category
$router->post("/insertproductcategory", "ProductCategoryController@insertproductcategory");
$router->get("/listproductcategory", "ProductCategoryController@index");
$router->patch("/updateproductcategory/{id}", "ProductCategoryController@updateproductcategory");
$router->delete("/deleteproductcategory/{id}", "ProductCategoryController@destroy");
//order
$router->post("/insertorder", "OrderController@insertorder");
$router->get("/listorder", "OrderController@index");
//update order list
$router->get("/listorderlist/{id}", "OrderListController@index");
$router->patch("/updateorderlist/{id}", "OrderListController@updateorderlist"); 
$router->patch("/updateorderliststatus/{id}", "OrderListController@updateorderliststatus"); 
$router->delete("/deleteorderlist/{id}", "OrderListController@destroy");
//invoice
$router->post("/insertinvoice", "InvoiceController@insertinvoice"); //cetak invoice
$router->patch("/updateinvoice/{id}", "InvoiceController@updateinvoice"); //bayar invoice
$router->delete("/deleteinvoice/{id}", "InvoiceController@destroy");
//income -> terakhir
$router->get("/listincome", "IncomeController@index");
$router->delete("/deleteincome/{id}", "IncomeController@destroy");
//partner (rekan)
$router->post("/insertpartner", "PartnerController@insertpartner");
$router->get("/listpartner", "PartnerController@index");
$router->patch("/updatepartner/{id}", "PartnerController@updatepartner");
$router->delete("/deletepartner/{id}", "PartnerController@destroy");
//payment
$router->post("/insertpayment", "PaymentController@insertpayment");
$router->get("/listpayment", "PaymentController@index");
$router->patch("/updatepayment/{id}", "PaymentController@updatepayment");
$router->delete("/deletepayment/{id}", "PaymentController@destroy");
//vendor (agen)
$router->get("/listvendor", "VendorController@index");
$router->post("/insertvendor", "VendorController@insertvendor");
$router->patch("/updatevendor/{id}", "VendorController@updatevendor");
$router->delete("/deletevendor/{id}", "VendorController@destroy");