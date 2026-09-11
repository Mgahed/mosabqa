<?php

use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\RegistrationSettingController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\TranslationController;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

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

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function () {
    Livewire::setUpdateRoute(function ($handle) {
        return Route::post('/livewire/update', $handle);
    });
    Route::get('/', function () {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        } else {
            return redirect()->route('login');
        }
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::group(['middleware' => ['role:super-admin|admin']], function () {
            Route::resource('categories', CategoryController::class)->names('categories');
            Route::resource('questions', QuestionController::class)->names('questions');
            Route::resource('schools', SchoolController::class)->names('schools');
            Route::resource('all-users', UserController::class)->names('admin.users');
            Route::resource('lookups', LookupController::class)->names('lookups');

            Route::get('registration-settings', [RegistrationSettingController::class, 'index'])->name('registration-settings.index');
            Route::post('registration-settings', [RegistrationSettingController::class, 'update'])->name('registration-settings.update');


            Route::group(['prefix' => 'translation', 'namespace' => 'Translation',], function () {
                Route::get('translations', [TranslationController::class, 'index'])->name('translations.index');
                Route::get('jsonTranslation', [TranslationController::class, 'jsonTranslation'])->name('jsonTranslation');
                Route::get('translations-search', [TranslationController::class, 'search'])->name('translations.search');
                Route::post('translations-add-custom-key', [TranslationController::class, 'addCustomKey'])->name('translations.addCustomKey');

                Route::get('translations-item/{id?}', [TranslationController::class, 'show'])->name('translations-item');
                Route::post('translations-update', [TranslationController::class, 'update'])->name('translations.update');
                Route::post('translations-update-global', [TranslationController::class, 'updateGlobal'])->name('translations.updateGlobal');
                Route::get('translations-scan', [TranslationController::class, 'scanTranslation'])->name('translations.scan');
                Route::get('translations-publish', [TranslationController::class, 'publishTranslation'])->name('translations.publish');
                Route::get('translations-unpublished', [TranslationController::class, 'unpublishedTranslation'])->name('translations.unpublished');
                Route::get('translations-create', [TranslationController::class, 'newTranslation'])->name('translations.create');

                Route::get('translate-all/{code?}', [TranslationController::class, 'translateAll'])->name('translate-all');
                Route::get('initiate/{code?}', [TranslationController::class, 'initiate'])->name('initiate');
            });
        });
    });

    Route::post('/data-by-nid', [ProfileController::class, 'dataByNid'])->name('auth.get-user-by-nid');
});
Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
require __DIR__ . '/auth.php';
require __DIR__ . '/student.php';
