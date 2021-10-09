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

Route::get('/', function () {
    return view('welcome');
});

//Auth::routes(["register"=>false,'verify' => true]);
Auth::routes([
    'register' => false, // Registration Routes...
    'reset' => true, // Password Reset Routes...
    'verify' => true, // Email Verification Routes...
  ]);
Route::view('forgot_password', 'api.reset_password')->name('password.reset');
Route::get('/home', function () {
    return redirect('/dashboard');
});
//Route::get('/home', 'HomeController@index')->name('home');

Route::group(['namespace' => 'Admin'], function () {
    Route::group(['middleware' => ['auth','verified']], function() { 
        Route::get('/profile', ['as' => 'profile', 'uses' => 'UserController@profile']);    
        Route::post('/profile', ['as' => 'profile-update', 'uses' => 'UserController@updateProfile']); 
        Route::get('/change-password', ['as' => 'change-password', 'uses' => 'UserController@changePassword']); 
        Route::post('/change-password', ['as' => 'change-password-update', 'uses' => 'UserController@updatePassword']);          
        Route::get('/dashboard', ['as' => 'dashboard', 'uses' => 'DashboardController@index']);  
        Route::resource('/apis','ApiController');    
        Route::resource('/users','UserController');   
        Route::resource('/leagues','LeagueController'); 
        Route::get('/leagues-invitations', ['as' => 'leagues-invitations', 'uses' => 'LeagueController@leagueInvitations']); 
        Route::post('/delete-leagues-invitations', ['as' => 'delete-leagues-invitations', 'uses' => 'LeagueController@leagueinvitationdelete']); 
        Route::get('/leagues-joined', ['as' => 'leagues-joined', 'uses' => 'LeagueController@leagueJoined']);  
        Route::post('/delete-leagues-joined', ['as' => 'delete-leagues-joined', 'uses' => 'LeagueController@leaguejoineddelete']);  
        Route::post('/leagues-updateLeague', ['as' => 'leagues-updateLeague', 'uses' => 'LeagueController@updateLeague']);    
        Route::post('/league-by-id', ['as' => 'league-by-id', 'uses' => 'LeagueController@leagueDetail']);           
        Route::resource('/games','GameController');             
    });
});



