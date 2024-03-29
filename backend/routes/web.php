<?php

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
use App\Http\Controllers\Auth\VerificationController;


Route::get('/', function () {
    return redirect('login');
});

//Clear Cache facade value:
Route::get('/cache-clear', function() {
    Artisan::call('optimize:clear');
    return '<h1>All Cache cleared</h1>';
});

Auth::routes(['verify' => true]);

Route::get('email/verify/{id}/{hash}', [VerificationController::class,'verify'])->name('verification.verify');

Route::group(['middleware' => ['web', 'guest'], 'as' => 'auth.','prefix'=>''], function () {    
    Route::view('signup', 'auth.admin.register')->name('register');
    Route::view('login', 'auth.admin.login')->name('login');
    Route::view('forget-password', 'auth.admin.forget-password')->name('forget-password');
    Route::view('reset-password/{token}/{email}', 'auth.admin.reset-password')->name('reset-password');
 
});    

Route::group(['middleware' => ['auth','preventBackHistory']], function () {
    Route::view('admin/profile', 'auth.profile.index')->name('auth.admin-profile');
    Route::group(['as' => 'admin.','prefix'=>'admin'], function () {        
        Route::view('dashboard', 'admin.index')->name('dashboard');
        Route::view('plan', 'admin.plan.index')->name('plan');
        Route::view('video', 'admin.video.index')->name('video');
        Route::view('addon', 'admin.addon.index')->name('addon');
        Route::view('setting', 'admin.setting.index')->name('setting');
        Route::view('seller', 'admin.seller.index')->name('seller');
        Route::view('buyer', 'admin.buyer.index')->name('buyer');
    });
});