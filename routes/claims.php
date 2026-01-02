<?php

use App\Http\Controllers\ClaimAmountController;
use App\Http\Controllers\ClaimController;
use App\Http\Middleware\confirmPassword;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::resource('claims', ClaimController::class);
    Route::get('claim/delete/{ref}', [ClaimController::class, 'delete'])->name('claim.delete')->middleware(confirmPassword::class);
    Route::get('claim/status/{status}/{ref}', [ClaimController::class, 'status'])->name('claim.status');

    Route::resource('claim_amount', ClaimAmountController::class);
    Route::get('claim_amount/receiving/{id}', [ClaimAmountController::class, 'receiving'])->name('claim_amount.receiving');
    Route::post('claim_amount/receiving/{id}', [ClaimAmountController::class, 'receivingStore'])->name('claim_amount.receivingStore');
    Route::get('claim_amount/delete/{ref}', [ClaimAmountController::class, 'delete'])->name('claim_amount.delete')->middleware(confirmPassword::class);
});
