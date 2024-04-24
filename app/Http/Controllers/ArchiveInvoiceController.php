<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArchiveInvoiceController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:ارشيف الفواتير'], ['only' => ['index', 'show']]);
        $this->middleware(['permission:ارشيف الفواتير'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:ارشيف الفواتير'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:ارشيف الفواتير'], ['only' => ['destroy']]);
    }

    public function index()
    {
        $invoices = Invoice::onlyTrashed()->get();
        return view('invoices.archive_invoice' , compact("invoices"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $id = $request->invoice_id;
        $invoice = Invoice::onlyTrashed()->findOrFail($id);
        $invoice->restore();
        session()->flash("restore_invoice");
        return redirect(url("invoices"));

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->invoice_id;
        $invoice = Invoice::onlyTrashed()->findOrFail($id);
        $invoice_number = $invoice->invoice_number;
        Storage::disk("attachments")->deleteDirectory($invoice_number);
        $invoice->ForceDelete();
        session()->flash("delete_invoice");
        return redirect()->back();
    }
}
