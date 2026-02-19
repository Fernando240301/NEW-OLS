<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\JenisPeralatanController;
use App\Http\Controllers\TypePeralatanController;
use App\Http\Controllers\KategoriPeralatanController;
use App\Http\Controllers\JenisLayananController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\PenawaranController;
use App\Http\Controllers\PPJBController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AccountTypeController;
use App\Http\Controllers\AccountCategoryController;
use App\Http\Controllers\ChartOfAccountController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApprovalController;
use Illuminate\Support\Facades\Mail;


// Tampilkan halaman login
Route::get('/', [AuthController::class, 'login'])->name('login');

// Proses login (POST)
Route::post('/proseslogin', [AuthController::class, 'proseslogin'])->name('proseslogin');

// Dashboard
Route::get('/main', [MainController::class, 'index'])
    ->middleware('auth')
    ->name('main');

// Client
Route::get('/client', [ClientController::class, 'index'])
    ->middleware('auth')
    ->name('main');

// Add/Edit/Delete Client
Route::middleware('auth')->group(function () {
    Route::get('/client', [ClientController::class, 'index'])->name('client.index');
    Route::get('/client/create', [ClientController::class, 'create'])->name('client.create');
    Route::post('/client', [ClientController::class, 'store'])->name('client.store');
    Route::get('/client/{id}/edit', [ClientController::class, 'edit'])->name('client.edit');
    Route::put('/client/{id}', [ClientController::class, 'update'])->name('client.update');
    Route::delete('/client/{id}', [ClientController::class, 'delete'])->name('client.delete');
});

// Log Activity
Route::get('/logactivity', [AuthController::class, 'logactivity'])
    ->middleware('auth')
    ->name('logactivity');


// Register
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Logout
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

//Activity Log
Route::get('/activity-log', function () {
    return view('activity.index');
})->middleware('auth')->name('activity.log');

//jenis peralatan
Route::get('/jenis-peralatan', [JenisPeralatanController::class, 'index'])
    ->middleware('auth')
    ->name('jenis_peralatan.index');

//Tambah Data
Route::get('/jenis_peralatan/create', [JenisPeralatanController::class, 'create'])->name('jenis_peralatan.create');

Route::post('/tambahperalatan', [JenisPeralatanController::class, 'store'])
    ->middleware('auth')
    ->name('jenis_peralatan.store');

//Update Data
Route::get('/jenis-peralatan/{id}/edit', [JenisPeralatanController::class, 'edit'])
    ->name('jenis_peralatan.edit');

Route::put('/jenis-peralatan/{id}', [JenisPeralatanController::class, 'update'])
    ->name('jenis_peralatan.update');

// Delete Data
Route::delete('/jenis-peralatan/{id}', [JenisPeralatanController::class, 'delete'])
    ->name('jenis_peralatan.delete');

// Kategori Peralatan
Route::get('/kategori-peralatan', [KategoriPeralatanController::class, 'index'])->name('kategori_peralatan.index');

// Tambah Data
Route::get('/kategori_peralatan/create', [KategoriPeralatanController::class, 'create'])
    ->middleware('auth')
    ->name('kategori_peralatan.create');

Route::post('/kategoriperalatan', [KategoriPeralatanController::class, 'store'])->name('kategori_peralatan.store');

// Update Data
Route::get('/kategori_peralatan/{id}/edit', [KategoriPeralatanController::class, 'edit'])
    ->name('kategori_peralatan.edit');

Route::put('/kategori_peralatan/{id}', [KategoriPeralatanController::class, 'update'])
    ->name('kategori_peralatan.update');

// Hapus Data
Route::delete('/kategori_peralatan/{id}', [KategoriPeralatanController::class, 'delete'])->name('kategori_peralatan.delete');

Route::get('/typeperalatan/create', [TypePeralatanController::class, 'create'])->name('typeperalatan.create');
Route::post('/typeperalatan', [TypePeralatanController::class, 'store'])->name('typeperalatan.store');

// Jenis Layanan
Route::get('/jenis-layanan', [JenisLayananController::class, 'index'])->name('jenis_layanan.index');

// Tambah Data
Route::get('/jenis_layanan/create', [JenisLayananController::class, 'create'])
    ->middleware('auth')
    ->name('jenis_layanan.create');

Route::post('/jenis_layanan', [JenisLayananController::class, 'store'])->name('jenis_layanan.store');

// Update Data
Route::get('/jenis_layanan/{id}/edit', [JenisLayananController::class, 'edit'])
    ->name('jenis_layanan.edit');

Route::put('/jenis_layanan/{id}', [JenisLayananController::class, 'update'])
    ->name('jenis_layanan.update');

// Hapus Data
Route::delete('/jenis_layanan/{id}', [JenisLayananController::class, 'delete'])->name('jenis_layanan.delete');

// Add/Edit/Delete Work Assignment
Route::middleware('auth')->group(function () {
    Route::get('/work-assignment', [MarketController::class, 'index'])->name('work_assignment.index');
    Route::get('/work-assignment/create', [MarketController::class, 'create'])->name('work_assignment.create');
    Route::post('/work-assignment', [MarketController::class, 'store'])->name('work_assignment.store');
    Route::get('/work-assignment/{id}/edit', [MarketController::class, 'edit'])->name('work_assignment.edit');
    Route::put('/work-assignment/{id}', [MarketController::class, 'update'])->name('work_assignment.update');
    Route::delete('/work-assignment/{id}', [MarketController::class, 'delete'])->name('work_assignment.delete');

    Route::post('/work-assignment/scope/store', [MarketController::class, 'storeScope'])->name('work_assignment.scope.store');
    Route::get('/work-assignment/{workflowid}/scope', [MarketController::class, 'getScope'])->middleware('auth');

    Route::get('/work-assignment/{id}/pdf', [MarketController::class, 'pdf'])->name('work_assignment.pdf');
    Route::get('/verifikasi/work-assignment/{id}/preview', [MarketController::class, 'previewGabungan'])->name('verifikasi.preview');
});

// Verifikasi WA
Route::get('/verifikasi/work-assignment', [MarketController::class, 'verifikasiIndex'])->name('verifikasi.work_assignment');
Route::post('/verifikasi/work-assignment/{id}/approve', [MarketController::class, 'approveMM'])->name('verifikasi.mm.approve');

Route::get('/approval/mo/{token}', [ApprovalController::class, 'approveMO'])
    ->name('approval.mo');

Route::get('/approval/mf/{token}', [ApprovalController::class, 'approveMF'])
    ->name('approval.mf');

// Project List
Route::middleware('auth')->group(function () {
    Route::get('/project-list', [OperationController::class, 'index'])->name('project_list.index');
    Route::get('/project-list/{id}/detail', [OperationController::class, 'detail'])->name('project_list.detail');
    Route::get('/project-list/scope/get', [OperationController::class, 'getScope'])->name('project_list.scope.get');
    Route::get('/kontrak/view/{filename}', [OperationController::class, 'viewKontrak'])->where('filename', '.*')->name('kontrak.view');
    Route::post('/project-list/file/upload', [OperationController::class, 'uploadMarketingFile'])->name('project_list.file.upload');

    //Surat Instruksi Kerja
    Route::get('/project-list/{id}/sik', [OperationController::class, 'sik'])->name('project_list.sik');
    Route::get('/project-list/{id}/createsik', [OperationController::class, 'createsik'])->name('sik.create');
    Route::post('/project-list/storesik', [OperationController::class, 'storesik'])->name('sik.store');
    Route::get('/sik/get-leader-data/{workflowid}/{userid}', [OperationController::class, 'getLeaderData']);
    Route::get('/sik/preview/{id}', [OperationController::class, 'previewSik'])->name('sik.show');


    Route::get('/project/{projectId}/sik/{id}/edit', [OperationController::class, 'editsik'])->name('sik.edit');
    Route::put('/project/{projectId}/sik/{id}', [OperationController::class, 'updatesik'])->name('sik.update');
    Route::delete('/project/{projectId}/sik/{id}', [OperationController::class, 'deletesik'])->name('sik.delete');

    Route::get('/project-list/{projectId}/sik/{id}/extend', [OperationController::class, 'extendsik'])->name('sik.extend');
    Route::post('/project-list/{projectId}/sik/{id}/extend', [OperationController::class, 'storeExtend'])->name('sik.storeExtend');

    //Documents
    Route::get('/documents/{workflow}/{folder?}', [DocumentController::class, 'documents'])->name('documents.index');
    Route::post('/documents/upload', [DocumentController::class, 'upload'])->name('documents.upload');
    Route::post('/documents/folder', [DocumentController::class, 'createFolder'])->name('documents.folder');
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::post('/documents/rename/{id}', [DocumentController::class, 'rename'])->name('documents.rename');
    // ✅ TARUH INI PALING ATAS
    Route::get('/documents/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/{workflowid}/{folderId?}', [DocumentController::class, 'documents'])->name('documents.index');
});

// Finance (New)
Route::middleware('auth')->group(function () {
    Route::resource('account-types', AccountTypeController::class);
    Route::resource('account-categories', AccountCategoryController::class);
    Route::resource('chart-of-accounts', ChartOfAccountController::class)->middleware('auth');
    Route::post('/chart-of-accounts/import', [ChartOfAccountController::class, 'import'])->name('chart-of-accounts.import');
    Route::get('chart-of-accounts/generate-code/{parentId}',[ChartOfAccountController::class, 'generateNextCode'])->name('chart-of-accounts.generate-code');
});





// DAFIT
// Route::get('/typeperalatan/create', [TypePeralatanController::class, 'create'])->name('typeperalatan.create');
// Route::post('/typeperalatan', [TypePeralatanController::class, 'store'])->name('typeperalatan.store');


// Tampilkan form edit (alat)
Route::get('/typeperalatan/{id}/edit', [TypePeralatanController::class, 'edit'])
    ->name('tambahtype.edit');

//update (type)
Route::put('/typeperalatan/{id}', [TypePeralatanController::class, 'update'])
    ->name('tambahtype.update');

Route::delete('/typeperalatan/{id}', [TypePeralatanController::class, 'destroy'])
    ->name('tambahtype.destroy');
// Type Peralatan
Route::get('/typeperalatan', [TypePeralatanController::class, 'index'])
    ->middleware('auth')
    ->name('typeperalatan');

//tambah
//edit

//Add/Edit/Delete Prospect
Route::middleware('auth')->group(function () {
    Route::get('/prospect', [ProspectController::class, 'index'])->name('prospect.index');
    Route::get('/prospect/create', [ProspectController::class, 'create'])->name('prospect.create');
    Route::post('/prospect', [ProspectController::class, 'store'])->name('prospect.store');
    Route::get('/prospect/{id}/edit', [ProspectController::class, 'edit'])->name('prospect.edit');
    Route::put('/prospect/{id}', [ProspectController::class, 'update'])->name('prospect.update');
    Route::delete('/prospect/{id}', [ProspectController::class, 'delete'])->name('prospect.delete');
});
//Add/Edit/Delete Penawaran
Route::middleware('auth')->group(function () {

    // INDEX
    Route::get('/penawaran', [PenawaranController::class, 'index'])
        ->name('penawaran.index');

    // CREATE
    Route::get('/penawaran/create', [PenawaranController::class, 'create'])
        ->name('penawaran.create');

    // STORE (INI YANG TADI HILANG ❗)
    Route::post('/penawaran', [PenawaranController::class, 'store'])
        ->name('penawaran.store');

    // UPLOAD FILE (SETELAH ADA ID)
    Route::get('/penawaran/{id}/upload', [PenawaranController::class, 'upload'])
        ->name('penawaran.upload');

    Route::post('/penawaran/{id}/upload', [PenawaranController::class, 'uploadStore'])
        ->name('penawaran.upload.store');

    // EDIT
    Route::get('/penawaran/{id}/edit', [PenawaranController::class, 'edit'])
        ->name('penawaran.edit');

    // UPDATE
    Route::put('/penawaran/{id}', [PenawaranController::class, 'update'])
        ->name('penawaran.update');

    // DELETE
    Route::delete('/penawaran/{id}', [PenawaranController::class, 'delete'])
        ->name('penawaran.delete');

    // APPROVE 
    Route::post('/penawaran/{id}/approve', [PenawaranController::class, 'approve'])
        ->name('penawaran.approve');
    Route::post(
        '/penawaran/{id}/revisi',
        [App\Http\Controllers\PenawaranController::class, 'revisi']
    )->name('penawaran.revisi');

    //Add/Edit/Delete Prospect
    Route::middleware('auth')->group(function () {
        Route::get('/ppjb', [PPJBController::class, 'index'])->name('ppjb.index');
        Route::get('/ppjb/create', [PPJBController::class, 'create'])->name('ppjb.create');
        Route::post('/ppjb', [PPJBController::class, 'store'])->name('ppjb.store');
        Route::get('/ppjb/{id}/edit', [PPJBController::class, 'edit'])->name('ppjb.edit');
        Route::put('/ppjb/{id}', [PPJBController::class, 'update'])->name('ppjb.update');
        Route::delete('/ppjb/{id}', [PPJBController::class, 'delete'])->name('ppjb.delete');
        Route::get('/ppjb/{id}/preview', [PPJBController::class, 'preview'])
            ->name('ppjb.preview');
        Route::post('/ppjb/{id}/approve', [PPJBController::class, 'approve'])
            ->name('ppjb.approve');
        Route::post('/ppjb/{id}/reject', [PPJBController::class, 'reject'])
            ->name('ppjb.reject');
    });
});
