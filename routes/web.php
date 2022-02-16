<?php

use App\Services\Hikvision\HikvisionService;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
//    $service = app(\App\Services\Hikvision\HikvisionInterface::class);
//    $data = $service->storeData();
//    dd($data);
    $server_name = "ALEJO\MSSQLSERVER";
    $database_name = "hikvision_api";
    try {
        $conn = new PDO("sqlsrv:server=$server_name,1433;Database=$database_name", "hikvision_api", "Ambush33");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        dd($e->getMessage());
    }
});
