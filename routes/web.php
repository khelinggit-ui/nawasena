<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LockScreenController;
use App\Http\Controllers\AnalisisKreditController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\EcommerceController;
use App\Http\Controllers\DebiturController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\WhatsappBotController;



Route::get('/', function () {
    return view('auth.login');
});

Route::group(['middleware'=>'auth'],function()
{
    Route::get('home',function()
    {
        return view('dashboard.home');
    });
    Route::get('home',function()
    {
        return view('dashboard.home');
    });
});

Auth::routes(['verify' => true]);

Route::group(['namespace' => 'App\Http\Controllers\Auth'],function()
{
    // ----------------------------- login ------------------------------------//
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'login')->name('login');
        Route::post('/login', 'authenticate');
        Route::get('logout/page', 'logoutPage')->name('logout.page');
    });

    // ----------------------------- register -------------------------------//
    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'register')->name('register');
        Route::post('/register','storeUser')->name('register.store');    
    });

    // ----------------------------- Forget Password --------------------------//
    // Auth::routes() handles password reset, using custom controllers below if needed
    // Route::controller(ForgotPasswordController::class)->group(function () {
    //     Route::get('forget-password', 'showLinkRequestForm')->name('password.request');    
    //     Route::post('forget-password', 'sendResetLinkEmail')->name('password.email');    
    // });

    // // ---------------------------- Reset Password ----------------------------//
    // Route::controller(ResetPasswordController::class)->group(function () {
    //     Route::get('reset-password/{token}', 'getPassword')->name('password.reset');
    //     Route::post('reset-password', 'updatePassword')->name('password.update');    
    // });

    // Lock the screen
    Route::get('/lock', function () {
        session(['locked' => true]);
        return redirect()->route('lockscreen')->with('success', 'Locked successfully!');
    })->name('lock-activate');

    Route::controller(LockScreenController::class)->group(function () {
        // ---------------------------- Lock Screen ---------------------------//
        Route::get('lockscreen', 'lockscreen')->name('lockscreen');
        Route::post('unlock',  'unlock')->name('unlock-screen');
    });

});

Route::group(['namespace' => 'App\Http\Controllers'],function()
{
    Route::middleware('auth')->group(function () {
        // --------------------- dashboard ------------------//
        Route::controller(HomeController::class)->group(function () {
            Route::get('home', 'index')->name('home');
            Route::get('summary', 'summary')->name('summary');
            Route::get('summary-spv', 'summarySpv')->name('summary.spv');
            Route::get('home/grafik-pencapaian', 'grafikPencapaian')->name('home.grafik-pencapaian');
            Route::get('home/grafik-pencapaian-cabang', 'grafikPencapaianCabang')->name('home.grafik-pencapaian-cabang');
        });
    });

    Route::middleware('auth')->group(function () {
        // --------------------- Pages ------------------//
        Route::prefix('pages')->group(function () {
            Route::controller(PagesController::class)->group(function () {
                Route::get('profile', 'profile')->name('pages.profile');
                Route::get('settings', 'settings')->name('pages.settings');
                Route::get('faqs', 'faqs')->name('pages.faqs');
            });
        });
    });

    Route::middleware('auth')->group(function () {
        // --------------------- Ecommerce ------------------//
        Route::prefix('apps-ecommerce')->group(function () {
            Route::controller(EcommerceController::class)->group(function () {
                Route::get('products', 'products')->name('ecommerce.products');
                Route::get('product-details', 'productDetails')->name('ecommerce.product-details');
                Route::get('add-product', 'addProduct')->name('ecommerce.add-product');
                Route::get('orders', 'orders')->name('ecommerce.orders');
                Route::get('order-details', 'orderDetails')->name('ecommerce.order-details');
                Route::get('customers', 'customers')->name('ecommerce.customers');
                Route::get('cart', 'cart')->name('ecommerce.cart');
                Route::get('checkout', 'checkout')->name('ecommerce.checkout');
                Route::get('seller-details', 'sellerDetails')->name('ecommerce.seller-details');
                Route::get('sellers', 'sellers')->name('ecommerce.sellers');
            });
            Route::controller(DebiturController::class)->group(function () {
                Route::get('debitur', 'index')->name('debitur.index');
                Route::get('debitur-approve', 'approvedIndex')->middleware('permission:page debitur approve')->name('debitur.approved');
                Route::get('debitur-approve/export', 'exportApproved')->middleware('permission:page debitur approve')->name('debitur.approved.export');
                Route::get('debitur-approve/summary', 'getApprovedSummary')->middleware('permission:page debitur approve')->name('debitur.approved.summary');
                Route::get('debitur-laporan-nominatif', 'nominativeReport')->middleware('permission:page laporan nominatif')->name('debitur.laporan.nominatif');
                Route::get('debitur-laporan-nominatif/export', 'exportNominativeReport')->middleware('permission:page laporan nominatif')->name('debitur.laporan.nominatif.export');
                Route::get('debitur-data', 'getData')->name('debitur.data');
                Route::get('debitur-details', 'details')->name('debitur.details');
                Route::post('debitur-payment', 'storePayment')->name('debitur.payment');
                Route::get('debitur-tracking', 'tracking')->name('debitur.tracking');
                Route::get('debitur-tracking-data', 'getTrackingData')->name('debitur.tracking.data');
                
                // Tahap Routes
                Route::get('debitur/create', 'create')->name('debitur.create');
                Route::post('debitur/store', 'store')->name('debitur.store');
                Route::post('debitur/store-step1', 'storeStep1')->name('debitur.store.step1');
                Route::put('debitur/{id}/store-step2', 'storeStep2')->name('debitur.store.step2');
                Route::put('debitur/{id}/store-step3', 'storeStep3')->name('debitur.store.step3');
                Route::delete('debitur/reset-draft', 'resetDraft')->name('debitur.reset.draft');
                Route::post('debitur/{id}/reapply', 'reapply')->name('debitur.reapply');
                Route::get('debitur/{id}/edit', 'edit')->name('debitur.edit');
                Route::put('debitur/{id}/update', 'updateMarketing')->name('debitur.update');
                Route::get('debitur/{id}/print-angsuran', 'printAngsuran')->name('debitur.print.angsuran');
                Route::get('debitur/{id}/edit-tahap1', 'editTahap1')->name('debitur.edit.tahap1');
                // Route::get('debitur/{id}/edit-tahap2', 'editTahap2')->name('debitur.edit.tahap2');
                // Route::put('debitur/{id}/update-tahap2', 'updateTahap2')->name('debitur.update.tahap2');
                Route::get('debitur/{id}/edit-tahap3-analis', 'editTahap3Analis')->name('debitur.edit.tahap3.analis');
                Route::put('debitur/{id}/update-tahap3-analis', 'updateTahap3Analis')->name('debitur.update.tahap3.analis');
                Route::get('debitur/{id}/edit-tahap3-risk', 'editTahap3Risk')->name('debitur.edit.tahap3.risk');
                Route::put('debitur/{id}/update-tahap3-risk', 'updateTahap3Risk')->name('debitur.update.tahap3.risk');
                Route::get('debitur/{id}/edit-tahap-mitra', 'editTahapMitra')->name('debitur.edit.tahap.mitra');
                Route::put('debitur/{id}/update-tahap-mitra', 'updateTahapMitra')->name('debitur.update.tahap.mitra');
                Route::post('debitur/{id}/confirm-dsr-penurunan', 'confirmDsrPenurunanPlafond')->name('debitur.confirm.dsr.penurunan');
                Route::post('debitur/{id}/confirm-takeout', 'confirmTakeOut')->name('debitur.confirm.takeout');
                
                // Review Action
                Route::post('debitur/{id}/action', 'action')->name('debitur.action');
                Route::get('debitur/{id}/download-all', 'downloadSemuaFile')->name('debitur.download.all');
                Route::get('debitur/export/mitra', 'exportMitra')->name('debitur.export.mitra');
            });

            Route::controller(AnalisisKreditController::class)->group(function () {
                Route::get('analisis-kredit', 'index')
                    ->middleware('permission:page analisis kredit|page analis')
                    ->name('analisis-kredit.index');
                Route::get('analisis-kredit-data', 'getData')
                    ->middleware('permission:page analisis kredit|page analis')
                    ->name('analisis-kredit.data');
                Route::get('analisis-kredit/{id}/view', 'view')
                    ->middleware('permission:page analis detail')
                    ->name('analisis-kredit.view');
                Route::get('analisis-kredit/{id}/edit', 'edit')->name('analisis-kredit.edit');
                Route::put('analisis-kredit/{id}', 'update')->name('analisis-kredit.update');
                Route::get('analisis-kredit/{id}/persetujuan', 'persetujuan')
                    ->middleware('permission:page debitur approve')
                    ->name('analisis-kredit.persetujuan');
                Route::put('analisis-kredit/{id}/persetujuan', 'approvePersetujuan')
                    ->middleware('permission:page debitur approve')
                    ->name('analisis-kredit.approve');
                Route::get('analisis-kredit/{id}/print', 'print')->name('analisis-kredit.print');
            });

            // IndoRegion API Routes
            Route::get('wilayah/provinsi', function () {
                return response()->json(\App\Models\Province::orderBy('name')->get());
            })->name('wilayah.provinsi');

            Route::get('wilayah/kabupaten/{province_id}', function ($province_id) {
                return response()->json(\App\Models\Regency::where('province_id', $province_id)->orderBy('name')->get());
            })->name('wilayah.kabupaten');

            Route::get('wilayah/kecamatan/{regency_id}', function ($regency_id) {
                return response()->json(\App\Models\District::where('regency_id', $regency_id)->orderBy('name')->get());
            })->name('wilayah.kecamatan');

            Route::get('wilayah/kelurahan/{district_id}', function ($district_id) {
                return response()->json(\App\Models\Village::where('district_id', $district_id)->orderBy('name')->get());
            })->name('wilayah.kelurahan');
        });
    });

    Route::middleware('auth')->group(function () {
        // --------------------- Invoices ------------------//
        Route::prefix('apps-invoices')->group(function () {
            Route::controller(InvoicesController::class)->group(function () {
                Route::get('list', 'list')->name('invoices.list');
                Route::get('create', 'create')->name('invoices.create');
                Route::get('details', 'details')->name('invoices.details');
            });
        });
    });

    Route::middleware('auth')->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::resource('roles', \App\Http\Controllers\RoleController::class);
        Route::resource('permissions', \App\Http\Controllers\PermissionController::class);
        Route::resource('cabangs', \App\Http\Controllers\CabangController::class);
    });

    Route::middleware(['auth', 'permission:menu whatsapp api'])->group(function () {
        // Bot pengingat WhatsApp. Harus di atas resource 'whatsapp' agar tidak
        // tertangkap sebagai {instanceName}.
        Route::prefix('whatsapp/bot')->name('whatsapp.bot.')->group(function () {
            Route::get('/', [WhatsappBotController::class, 'index'])
                ->middleware('permission:page whatsapp bot setting')->name('index');
            Route::get('dashboard', [WhatsappBotController::class, 'dashboard'])
                ->middleware('permission:page whatsapp bot dashboard')->name('dashboard');
            Route::post('settings', [WhatsappBotController::class, 'updateSettings'])
                ->middleware('permission:page whatsapp bot setting')->name('settings.update');
            Route::get('templates', [WhatsappBotController::class, 'templates'])
                ->middleware('permission:page whatsapp template')->name('templates');
            Route::post('templates', [WhatsappBotController::class, 'updateTemplates'])
                ->middleware(['permission:page whatsapp template', 'permission:update whatsapp template'])->name('templates.update');
            Route::get('logs', [WhatsappBotController::class, 'logs'])
                ->middleware('permission:page whatsapp log')->name('logs');
            Route::get('opt-outs', [WhatsappBotController::class, 'optOuts'])
                ->middleware('permission:page whatsapp opt out')->name('opt-outs');
            Route::post('opt-outs', [WhatsappBotController::class, 'storeOptOut'])
                ->middleware('permission:create whatsapp opt out')->name('opt-outs.store');
            Route::delete('opt-outs/{id}', [WhatsappBotController::class, 'destroyOptOut'])
                ->middleware('permission:delete whatsapp opt out')->name('opt-outs.destroy');
            Route::post('test-send', [WhatsappBotController::class, 'testSend'])
                ->middleware('permission:send whatsapp test')->name('test-send');
        });

        Route::get('whatsapp', [WhatsAppController::class, 'index'])
            ->middleware('permission:page whatsapp instance')->name('whatsapp.index');
        Route::post('whatsapp', [WhatsAppController::class, 'store'])
            ->middleware('permission:create whatsapp instance')->name('whatsapp.store');
        Route::get('whatsapp/{instanceName}', [WhatsAppController::class, 'show'])
            ->middleware('permission:page whatsapp instance')->name('whatsapp.show');
        Route::delete('whatsapp/{instanceName}', [WhatsAppController::class, 'destroy'])
            ->middleware('permission:delete whatsapp instance')->name('whatsapp.destroy');
        Route::get('whatsapp/{instanceName}/settings', [WhatsAppController::class, 'settings'])
            ->middleware('permission:page whatsapp instance')->name('whatsapp.settings');
        Route::post('whatsapp/{instanceName}/settings', [WhatsAppController::class, 'updateSettings'])
            ->middleware('permission:page whatsapp instance')->name('whatsapp.settings.update');
        Route::post('whatsapp/{instanceName}/disconnect', [WhatsAppController::class, 'disconnect'])
            ->middleware('permission:page whatsapp instance')->name('whatsapp.disconnect');
    });

    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::resource('perusahaan', \App\Http\Controllers\PerusahaanController::class);
        Route::resource('jabatans', \App\Http\Controllers\JabatanController::class);
        Route::resource('parameter-produk', \App\Http\Controllers\ParameterProdukController::class);
        Route::resource('kantors', \App\Http\Controllers\KantorController::class);
    });

});
