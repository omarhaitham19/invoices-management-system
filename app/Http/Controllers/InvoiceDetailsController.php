<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceAttachements;
use App\Models\InvoiceDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceDetailsController extends Controller
{

    function __construct()
    {
        $this->middleware(['permission:حذف المرفق'], ['only' => ['destroy']]);
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
        
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $invoice = Invoice::where("id" , $id)->first();
        $details = InvoiceDetails::where("invoice_id" , $id)->get();
        $attachments = InvoiceAttachements::where("invoice_id" , $id)->get();

        return view('invoices.invoices_details' , compact("invoice" , "details" , "attachments"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InvoiceDetails $invoiceDetails)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $invoice_attachment = InvoiceAttachements::findOrFail($request->id_file);
        $invoice_attachment->delete();
        
        $filePath = $request->invoice_number . '/' . $request->file_name;
    
        Storage::disk('attachments')->delete($filePath);
    
        session()->flash("delete", "تم حذف المرفق بنجاح");
        return redirect()->back();
    }
    


    public function open_file($invoice_number, $file_name)
{
    $disk = Storage::disk('attachments');
    
    if ($disk->exists($invoice_number . "/" . $file_name)) {
        
        $file_content = $disk->get($invoice_number . "/" . $file_name);
        
        $mime_type = $disk->mimeType($invoice_number . "/" . $file_name);
        
        return response($file_content)
            ->header('Content-Type', $mime_type)
            ->header('Content-Disposition', 'inline; filename="' . $file_name . '"');
    } else {
        return response()->json(['error' => 'File not found'], 404);
    }
} 

    public function download_file($invoice_number, $file_name)
{
    $disk = Storage::disk('attachments');
    
    if ($disk->exists($invoice_number . "/" . $file_name)) {

        $file_content = $disk->get($invoice_number . "/" . $file_name);
        
        $mime_type = mime_content_type($disk->path($invoice_number . "/" . $file_name));
        
        return response($file_content)
            ->header('Content-Type', $mime_type)
            ->header('Content-Disposition', 'attachment; filename="' . $file_name . '"');
    } else {
        return response()->json(['error' => 'File not found'], 404);
    }
}

}