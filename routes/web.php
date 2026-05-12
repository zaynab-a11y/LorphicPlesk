<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\BacklinkController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SeoIssueController;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Projects
    Route::resource('projects', ProjectController::class);

    Route::prefix('projects/{project}')->name('projects.')->group(function () {
        // Keywords
        Route::get('/keywords', [KeywordController::class, 'index'])->name('keywords.index');
        Route::post('/keywords', [KeywordController::class, 'store'])->name('keywords.store');
        Route::delete('/keywords/{keyword}', [KeywordController::class, 'destroy'])->name('keywords.destroy');
        Route::get('/keywords/{keyword}/history', [KeywordController::class, 'rankingHistory'])->name('keywords.history');

        // Backlinks
        Route::get('/backlinks', [BacklinkController::class, 'index'])->name('backlinks.index');
        Route::post('/backlinks', [BacklinkController::class, 'store'])->name('backlinks.store');
        Route::delete('/backlinks/{backlink}', [BacklinkController::class, 'destroy'])->name('backlinks.destroy');

        // Pages
        Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
        Route::get('/pages/{page}', [PageController::class, 'show'])->name('pages.show');

        // Issues
        Route::get('/issues', [SeoIssueController::class, 'index'])->name('issues.index');
        Route::post('/issues/{issue}/resolve', [SeoIssueController::class, 'resolve'])->name('issues.resolve');
        Route::post('/issues/bulk-resolve', [SeoIssueController::class, 'bulkResolve'])->name('issues.bulk-resolve');
    });
});

require __DIR__.'/auth.php';
