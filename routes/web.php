<?php

use App\Http\Controllers\DonationCasesController;
use App\Http\Controllers\DonationTargetController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\PaymentSuccessController;
use App\Http\Controllers\Reports\FamilyMemberReportController;
use App\Http\Controllers\Reports\FamilyReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin/login');
})->name('home');

Route::get('/public-home', LandingController::class)->name('public-home');
Route::get('/donation-cases', DonationCasesController::class)->name('donation-cases');
Route::get('/donation-cases/{donationTarget}', DonationTargetController::class)->name('donation-target');
Route::match(['GET', 'POST'], '/payments/paymob/callback', PaymentCallbackController::class)->name('payments.paymob.callback');
Route::get('/payments/{donation}/success', PaymentSuccessController::class)->name('payments.success');

Route::middleware(['auth'])->group(function () {
    Route::get('/reports/families/{family}', FamilyReportController::class)->name('reports.families.download');
    Route::get('/reports/family-members/{familyMember}', FamilyMemberReportController::class)->name('reports.family-members.download');
});
