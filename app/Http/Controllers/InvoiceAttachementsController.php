<?php

namespace App\Http\Controllers;

use App\Models\InvoiceAttachements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceAttachementsController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:اضافة مرفق'], ['only' => ['store']]);
    }


    public function index()
    {
        //
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
        $this->validate($request, [
            'file_name' => 'mimes:pdf,jpeg,png,jpg',
            ], [
                'file_name.mimes' => 'صيغة المرفق يجب ان تكون   pdf, jpeg , png , jpg',
            ]);
            $invoice_id = $request->invoice_id;
            $invoice_number = $request->invoice_number;
            $uploadedFile = $request->file('file_name');
            $originalFileName = $uploadedFile->getClientOriginalName();
            $fileName = time() . "_" . $originalFileName;
            $filePath = $uploadedFile->storeAs("public/attachments/$invoice_number", $fileName);
            InvoiceAttachements::create([
                "file_name" => $fileName,
                "invoice_number" => $invoice_number,
                "created_by" => Auth::user()->name,
                "invoice_id" => $invoice_id
            ]);
        session()->flash("success" , "تم حفظ المرفق بنجاح");
        return redirect()->back();

    }

    /**
     * Display the specified resource.
     */
    public function show(InvoiceAttachements $invoiceAttachements)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InvoiceAttachements $invoiceAttachements)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InvoiceAttachements $invoiceAttachements)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InvoiceAttachements $invoiceAttachements)
    {
        //
    }
}
