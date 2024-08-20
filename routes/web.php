<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LoanDetailController;
use App\Http\Controllers\EmiDetailController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/process_data', [LoanDetailController::class, 'process_data'])->name('process_data');
    Route::get('/emi_detail', [LoanDetailController::class, 'emi_detail'])->name('emi_detail');
    // Resources controllers
    Route::resource('loan_details', LoanDetailController::class);
});


require __DIR__.'/auth.php';
