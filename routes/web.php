<?php

use App\Http\Controllers\ValutsController;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\HomeController;

use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Account\ActivityLogController;
use App\Http\Controllers\Account\SettingController;


use App\Http\Controllers\UnderMaintenanceController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes(["register"=>false]);


// Authintication
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('home', [HomeController::class, 'index'])->name('home');


Route::group(['middleware' => 'auth'], function(){
    // Account
    Route::get('/user/profile',[ProfileController::class, 'userProfile'])->name('user.profile');
    Route::post('/user/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/user/setting',[SettingController::class, 'index'])->name('user.setting');
    Route::get('/user/activity',[ActivityLogController::class, 'index'])->name('user.activity');

    Route::get('vault', [ValutsController::class, 'index'])->name('vault');



    // Under Maintenance
    Route::get('/under-maintenance', [UnderMaintenanceController::class, 'index'])->name('under-maintenance');




    Route::group([], function () {
        if (app()->isLocal()) {

            /******************** MIGRATION ****************/
            Route::get("/migrate", function () {
                Artisan::call("migrate");
                return Artisan::output();
            });
            Route::get("/migrate/fresh", function () {
                Artisan::call("migrate:fresh");
                return Artisan::output();
            });

            Route::get("/storage/link", function () {
                Artisan::call("storage:link");
                return Artisan::output();
            });

            Route::get("/seed", function () {
                Artisan::call("db:seed");
                return Artisan::output();
            });
        }

        /******************** CACHE ****************/
        Route::get('/cache/clear', function () {
            $title = __('all.clear_cache');

            $output = "";
            Artisan::call('cache:clear');
            $output .= "<br/>";
            $output .= Artisan::output();
            Artisan::call('view:clear');
            $output .= "<br/>";
            $output .= Artisan::output();
            Artisan::call('route:clear');
            $output .= "<br/>";
            $output .= Artisan::output();
            Artisan::call('config:clear');
            $output .= "<br/>";
            $output .= Artisan::output();

            return $output;
        })->name("clear-cache");
    });

});
