<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
//Route::get('/test-api','api\LeagueController@callScheduleApi');
Route::get('/list','api\GameStockListController@index');
Route::get('/schedule','api\GameStockListController@schedule');
Route::get('/phpinfo','api\GameStockListController@phpinfo');

Route::post('/login', 'api\AuthController@login');
Route::post('/register', 'api\AuthController@register');
Route::post('/verify-otp', 'api\AuthController@verifyOTP');
Route::post('/resend-otp', 'api\AuthController@resendOTP');
Route::post('/forgot-password', 'api\AuthController@forgot');
Route::post('reset-password', 'api\AuthController@reset');
Route::get('/games', 'api\LeagueController@games');
Route::middleware(['auth:sanctum'])->group(function () {
  Route::post('/change-password', 'api\AuthController@changePassword');
  Route::post('/upload-profile-image', 'api\AuthController@uplodadProfileImage');
  Route::post('user-detail', 'api\AuthController@userDetail');
  Route::post('update-profile', 'api\AuthController@userUpdate');
  Route::get('/users', 'api\UserController@index');
  Route::post('/league-list', 'api\LeagueController@index');
  Route::post('/create-league', 'api\LeagueController@store');
  Route::post('/league-invitation', 'api\LeagueController@invite');
  Route::post('/league-detail', 'api\LeagueController@detail');
  Route::post('/join-league', 'api\LeagueController@join');
  Route::post('/logout', 'api\AuthController@userLogout');
  Route::post('/my-league', 'api\LeagueController@userLeagues');
  Route::post('/my-joined-league', 'api\LeagueController@userJoinedLeagues');
  Route::get('/notification-list', 'api\NotificationController@index');
  Route::post('/trade-list', 'api\LeagueController@tradelist');
  Route::post('/trade-detail', 'api\LeagueController@trade_detail');

  Route::post('/lederboard', 'api\LeagueController@lederboard');
  Route::post('/premium-update', 'api\LeagueController@premiumUpdate');
  Route::get('/account-detail', 'api\LeagueController@accountDetail');
  Route::post('/buy-share', 'api\LeagueController@buyShare');
  Route::post('/sell-share', 'api\LeagueController@sellShare');

  Route::get('/home', 'api\LeagueController@home');

  Route::get('/invitation-list', 'api\LeagueController@invitationList');

  Route::post('/home-graph', 'api\LeagueController@homeGraph');
  Route::post('/trade-detail-graph', 'api\LeagueController@tradeDetailGraph');
  Route::post('/free-trade-count', 'api\LeagueController@freeTradeCount');

});


Route::post('/nfl-standings', 'api\nflController@standings');
