<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{PublicController,AuthController,PortalController};
Route::get('/',[PublicController::class,'home'])->name('public.home');
Route::get('/unit/{slug}',[PublicController::class,'unit'])->name('public.unit');
Route::get('/prestasi',[PublicController::class,'listing'])->name('public.achievements');
Route::get('/media/{asset}',[PublicController::class,'media'])->name('public.media');
Route::get('/sitemap.xml',[PublicController::class,'sitemap']);
Route::get('/robots.txt',[PublicController::class,'robots']);
Route::middleware('guest')->group(function(){
 Route::get('/login',[AuthController::class,'login'])->name('login');
 Route::post('/login',[AuthController::class,'authenticate'])->middleware('throttle:20,1');
 Route::get('/password/forgot',[AuthController::class,'forgot'])->name('password.request');
 Route::post('/password/email',[AuthController::class,'email'])->middleware('throttle:5,10')->name('password.email');
 Route::get('/password/reset/{token}',[AuthController::class,'resetForm'])->name('password.reset');
 Route::post('/password/reset',[AuthController::class,'reset'])->middleware('throttle:5,10')->name('password.update');
});
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth')->name('logout');
Route::middleware(['auth','active'])->group(function(){
 Route::get('/akun/kata-sandi',[AuthController::class,'profile'])->name('profile.edit');
 Route::put('/akun/kata-sandi',[AuthController::class,'updatePassword'])->middleware('throttle:6,10')->name('profile.update');
 Route::get('/portal',[PortalController::class,'index'])->name('portal.index');
 Route::get('/portal/materi/{material}',[PortalController::class,'show'])->name('portal.show');
 Route::get('/berkas/{asset}',[PortalController::class,'file'])->middleware('throttle:120,1')->name('portal.file');
});
Route::prefix('admin')->name('admin.')->middleware(['auth','active','role:admin,editor'])->group(base_path('routes/admin.php'));
Route::get('/halaman/{slug}',[PublicController::class,'page'])->name('public.content');
Route::get('/{slug}',[PublicController::class,'page'])->where('slug','tentang|fasilitas|pendaftaran|kontak')->name('public.page');
