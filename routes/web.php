<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArchiveInvoiceController;
use App\Http\Controllers\ClientsReportController;
use App\Http\Controllers\InvoiceAttachementsController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceDetailsController;
use App\Http\Controllers\InvoiceReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\UserController;
use App\Models\InvoiceDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

Auth::routes();
Route::match(['get', 'post'], 'register', function(){
    return redirect('/');
    });

Route::middleware('auth')->group(function(){
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::resource('invoices', InvoiceController::class);
Route::resource('sections', SectionController::class);
Route::resource("products" , ProductController::class);
Route::resource("InvoiceAttachments" , InvoiceAttachementsController::class);
Route::get("/section/{id}" , [InvoiceController::class , "getProducts"]);
Route::get("/InvoicesDetails/{id}" , [InvoiceDetailsController::class , "show"]);
Route::get("view_file/{invoice_number}/{file_name}" , [InvoiceDetailsController::class , "open_file"]);
Route::get("download/{invoice_number}/{file_name}" , [InvoiceDetailsController::class , "download_file"]);
Route::post("delete_file" , [InvoiceDetailsController::class , "destroy"])->name("delete_file");
Route::get('/edit_invoice/{id}', [InvoiceController::class , "edit"] );
Route::patch("/invoices/update" , [InvoiceController::class , "update"]);
Route::delete('invoices', [InvoiceController::class , "destroy"])->name('invoices.destroy');
Route::get("paymentStatus/{id}" , [InvoiceController::class , "show"]);
Route::post("updateStatus" , [InvoiceController::class , "updateStatus"]);
Route::get("paidInvoices" , [InvoiceController::class , "PaidInvoices"]);
Route::get("UnpaidInvoices" , [InvoiceController::class , "UnpaidInvoices"]);
Route::get("PartialPaidInvoices" , [InvoiceController::class , "PartialPaidInvoices"]);
Route::get("MarkAsRead_all" , [InvoiceController::class , 'readNotifications']);
Route::resource("archiveInvoices" , ArchiveInvoiceController::class);
Route::patch("archiveInvoice/update" , [ArchiveInvoiceController::class , "update"]);
Route::delete("archiveInvoice/delete" , [ArchiveInvoiceController::class , "destroy"]);
Route::get("printInvoice/{id}" , [InvoiceController::class , "printInvoice"]);
Route::get("invoices_reports" , [InvoiceReportController::class , 'index']);
Route::post("search_invoices" , [InvoiceReportController::class , "searchInvoices"]);
Route::get("clients_reports" , [ClientsReportController::class , "index"]);
Route::post("Search_clients" , [ClientsReportController::class , "searchClients"]);
Route::redirect("index" , "home");
Route::resource('roles', RoleController::class);
Route::resource('users', UserController::class);
});

Route::get('/{page}', [AdminController::class , "index"]);