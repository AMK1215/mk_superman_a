<?php

use App\Http\Controllers\Admin\BannerTextController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Bank\BankController;
use App\Http\Controllers\Api\V1\BannerController;
use App\Http\Controllers\Api\V1\ContactController;
//use App\Http\Controllers\Api\V1\Game\LaunchGameController;
use App\Http\Controllers\Api\V1\Player\DepositController;
use App\Http\Controllers\Api\V1\Player\PlayerTransactionLogController;
use App\Http\Controllers\Api\V1\Player\TransactionController;
use App\Http\Controllers\Api\V1\Player\UserPaymentControler;
use App\Http\Controllers\Api\V1\Player\WagerController;
use App\Http\Controllers\Api\V1\Player\WithDrawController;
use App\Http\Controllers\Api\V1\PromotionController;
use App\Http\Controllers\Api\V1\GetBalanceController;
use App\Http\Controllers\Api\V1\Slot\GameController;
use App\Http\Controllers\Api\V1\Slot\LaunchGameController;
use App\Http\Controllers\Api\V1\Webhook\AdjustmentController;
use App\Http\Controllers\Api\V1\Webhook\BetController;
use App\Http\Controllers\Api\V1\Webhook\BetNResultController;
use App\Http\Controllers\Api\V1\Webhook\BetResultController;
use App\Http\Controllers\Api\V1\Webhook\CancelBetController;
use App\Http\Controllers\Api\V1\Webhook\CancelBetNResultController;
use App\Http\Controllers\Api\V1\Webhook\RewardController;
use App\Http\Controllers\TestController;
use App\Models\Admin\Role;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Slot\GetDaySummaryController;

//login route post
Route::post('/login', [AuthController::class, 'login']);

// logout
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('promotion', [PromotionController::class, 'index']);
Route::get('banner', [BannerController::class, 'index']);
Route::get('popup-ads-banner', [BannerController::class, 'AdsBannerIndex']);
Route::get('bannerText', [BannerController::class, 'bannerText']);
Route::get('v1/validate', [AuthController::class, 'callback']);
Route::get('gameTypeProducts/{id}', [GameController::class, 'gameTypeProducts']);
Route::get('allGameProducts', [GameController::class, 'allGameProducts']);
Route::get('gameType', [GameController::class, 'gameType']);
Route::get('gamelist/{product_id}/{game_type_id}', [GameController::class, 'gameList']);
Route::get('hotgamelist', [GameController::class, 'HotgameList']);
Route::post('Seamless/PullReport', [LaunchGameController::class, 'pullReport']);


// sameless route

Route::post('GetBalance', [GetBalanceController::class, 'getBalance']);
Route::post('BetNResult', [BetNResultController::class, 'handleBetNResult']);
Route::post('CancelBetNResult', [CancelBetNResultController::class, 'handleCancelBetNResult']);
Route::post('Bet', [BetController::class, 'handleBet']);
Route::post('Result', [BetResultController::class, 'handleResult']);
Route::post('CancelBet', [CancelBetController::class, 'handleCancelBet']);
Route::post('Adjustment', [AdjustmentController::class, 'handleAdjustment']);
Route::post('Reward', [RewardController::class, 'handleReward']);


// for slot
Route::post('/transaction-details/{tranId}', [GetDaySummaryController::class, 'getTransactionDetails']);


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('GameLogin', [LaunchGameController::class, 'LaunchGame']);
    Route::get('wager-logs', [WagerController::class, 'index']);
    Route::get('transactions', [TransactionController::class, 'index']);

    //logout
    Route::get('user', [AuthController::class, 'getUser']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('changePassword', [AuthController::class, 'changePassword']);
    Route::post('profile', [AuthController::class, 'profile']);
    Route::get('agent-payment-type', [UserPaymentControler::class, 'agentPayment']);
    Route::get('payment-type', [UserPaymentControler::class, 'paymentType']);
    Route::get('contact', [ContactController::class, 'index']);

    Route::group(['prefix' => 'transaction'], function () {
        Route::post('withdraw', [WithDrawController::class, 'withdraw']);
        Route::post('deposit', [DepositController::class, 'deposit']);
        Route::get('player-transactionlog', [PlayerTransactionLogController::class, 'index']);
        Route::get('deposit-requestlog', [TransactionController::class, 'depositRequestLog']);
        Route::get('withdraw-requestlog', [TransactionController::class, 'withDrawRequestLog']);
    });

    Route::group(['prefix' => 'bank'], function () {
        Route::get('all', [BankController::class, 'all']);
    });
    Route::group(['prefix' => 'game'], function () {
        Route::post('Seamless/LaunchGame', [LaunchGameController::class, 'launchGame']);
    });
    Route::group(['prefix' => 'direct'], function () {
        Route::post('Seamless/LaunchGame', [LaunchGameController::class, 'directLaunchGame']);
    });
});

Route::get('/game/gamelist/{provider_id}/{game_type_id}', [GameController::class, 'gameList']);