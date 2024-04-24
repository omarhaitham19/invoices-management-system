<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceAttachements;
use App\Models\InvoiceDetails;
use App\Models\Section;
use App\Models\User;
use App\Notifications\AddInvoice;
use App\Notifications\AddInvoiceNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{

    function __construct()
    {
        $this->middleware(['permission:قائمة الفواتير'], ['only' => ['index']]);
        $this->middleware(['permission:تغير حالة الدفع'], ['only' => ['show' , 'updateStatus']]);
        $this->middleware(['permission:اضافة فاتورة'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:تعديل الفاتورة'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:حذف الفاتورة'], ['only' => ['destroy']]);
        $this->middleware(['permission:ارشفة الفاتورة'], ['only' => ['destroy']]);
        $this->middleware(['permission:الفواتير المدفوعة'], ['only' => ['paidInvoices']]);
        $this->middleware(['permission:الفواتير الغير مدفوعة'], ['only' => ['UnpaidInvoices']]);
        $this->middleware(['permission:الفواتير المدفوعة جزئيا'], ['only' => ['PartialPaidInvoices']]);
        $this->middleware(['permission:طباعةالفاتورة'], ['only' => ['printInvoice']]);
    }

    public function index()
    {
        $invoices = Invoice::orderBy('id', 'desc')->get();
        return view('invoices.invoices' , compact("invoices"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sections = Section::all();
        return view('invoices.add_invoice' , compact("sections"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date',
            'product' => 'required|string',
            'section_id' => 'required|numeric',
            'amount_collection' => 'required|numeric',
            'amount_commission' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'value_VAT' => 'required|numeric',
            'rate_VAT' => 'required|regex:/^\d+(\.\d+)?%$/',
            'total' => 'required|numeric',
            'note' => 'nullable|string',
            "pic" => "mimetypes:image/jpeg,image/png,image/jpg,application/pdf"
        ],[
            'pic.mimetypes' => 'صيغة المرفق يجب ان تكون   pdf, jpeg , png , jpg',
            'invoice_number.unique' => 'رقم الفاتوره مسجل مسبقا',
        ]);
    
        Invoice::create([
            'invoice_number' => $validatedData['invoice_number'],
            'invoice_date' => $validatedData['invoice_date'],
            'due_date' => $validatedData['due_date'],
            'product' => $validatedData['product'],
            'amount_collection' => $validatedData['amount_collection'],
            'amount_commission' => $validatedData['amount_commission'],
            'discount' => $validatedData['discount'],
            'value_VAT' => $validatedData['value_VAT'],
            'rate_VAT' => $validatedData['rate_VAT'],
            'total' => $validatedData['total'],
            'status' => 'غير مدفوعة',
            'value_status' => 2,
            'note' => $validatedData['note'],
            "user" => Auth::user()->name,
            'section_id' => $validatedData['section_id'],
        ]);

        $invoice_id = Invoice::latest()->first()->id;
        InvoiceDetails::create([
            'invoice_id' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'section' => $request->section_id,
            'status' => 'غير مدفوعة',
            'value_status' => 2,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);

        if ($request->hasFile('pic')) {
            $invoice_number = $request->invoice_number;
            $uploadedFile = $request->file('pic');
            $originalFileName = $uploadedFile->getClientOriginalName();
            $fileName = time() . "_" . $originalFileName;
            $filePath = $uploadedFile->storeAs("public/attachments/$invoice_number", $fileName);
            InvoiceAttachements::create([
                "file_name" => $fileName,
                "invoice_number" => $invoice_number,
                "created_by" => Auth::user()->name,
                "invoice_id" => $invoice_id
            ]);
        }

        // $user = Auth::user();
        // Notification::send($user , new AddInvoice($invoice_id));

        $user = User::get();
        $invoice_id = Invoice::latest()->first()->id;
        Notification::send($user , new AddInvoiceNotification($invoice_id));

        session()->flash("success" , "تم حفظ البيانات بنجاح");
        return redirect()->back();
    }
    

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice , $id)
    {
        $invoice = Invoice::FindOrFail($id);
        return view('invoices.update_status')->with("invoice" , $invoice);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $invoice = Invoice::findOrFail($id);
    
        $sections = Section::all();
        
        return view('invoices.edit_invoice', ['invoice' => $invoice, 'sections' => $sections]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $invoice = Invoice::FindOrFail($request->invoice_id);
        $invoice_oldNumber = $invoice->invoice_number;
        $invoiceDetails = InvoiceDetails::where('invoice_id', $invoice->id)->first();
        $invoiceAttachments = InvoiceAttachements::where('invoice_id', $invoice->id)->get();

        $validatedData = $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number,'.$invoice->id.',id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date',
            'product' => 'required|string',
            'Section' => 'required|numeric',
            'amount_collection' => 'required|numeric',
            'amount_commission' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'value_VAT' => 'required|numeric',
            'rate_VAT' => 'required|regex:/^\d+(\.\d+)?%$/',
            'total' => 'required|numeric',
            'note' => 'nullable|string',
        ],[
            'invoice_number.unique' => 'رقم الفاتوره مسجل مسبقا'
        ]);

        $invoice->update([
            'invoice_number' => $validatedData['invoice_number'],
            'invoice_date' => $validatedData['invoice_date'],
            'due_date' => $validatedData['due_date'],
            'product' => $validatedData['product'],
            'amount_collection' => $validatedData['amount_collection'],
            'amount_commission' => $validatedData['amount_commission'],
            'discount' => $validatedData['discount'],
            'value_VAT' => $validatedData['value_VAT'],
            'rate_VAT' => $validatedData['rate_VAT'],
            'total' => $validatedData['total'],
            'note' => $validatedData['note'],
            "user" => Auth::user()->name,
            'section_id' => $validatedData['Section']
        ]);

        $invoiceDetails->update([
            'invoice_number' => $validatedData['invoice_number'],
            'product' => $validatedData['product'],
            'section' => $validatedData['Section'],
            'note' => $validatedData['note'],
            'user' => Auth::user()->name,
        ]);

        foreach ($invoiceAttachments as $attachment) {
            $attachment->update([
                'invoice_number' => $validatedData['invoice_number'],
            ]);
        }

        if ($invoice_oldNumber !== $validatedData['invoice_number']) {
            Storage::disk("attachments")->move($invoice_oldNumber, $validatedData['invoice_number']);
        }

        
        session()->flash("success" , "تم تعديل الفاتوره بنجاح");
        return redirect()->back();

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
            $id = $request->invoice_id;
            $invoice = Invoice::FindOrFail($id);
            $invoice_number = $invoice->invoice_number;
            $archive_id = $request->archive_id;

                if ($archive_id != 1) {
                    Storage::disk('attachments')->deleteDirectory($invoice_number);
                    $invoice->ForceDelete(); 
                    session()->flash('delete_invoice');
                    return redirect()->back();
                }else{
                    $invoice->delete();
                    session()->flash("archive_invoice");
                    return redirect(url('archiveInvoices'));
                }

    }

    public function getProducts($id)
    {
        $products =DB::table("products")->where("section_id", $id)->pluck("name", "id");
        return response()->json($products);
    }

    public function updateStatus(Request $request){
        $id = $request->invoice_id;
        $invoice = Invoice::FindOrFail($id);

        $data = $request->validate([
            'status'=>"required|string",
            'payment_date'=>"required|date",
            'invoice_number' => 'required|string',
            'product' => 'required|string',
            'Section' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        if ($data['status'] === "مدفوعة") {

            $invoice->update([
                'status' => $data['status'],
                'value_status' => 1,
                'payment_date' => $data['payment_date']
            ]);

            InvoiceDetails::create([
                'invoice_number' => $data['invoice_number'],
                'invoice_id' => $id,
                'product' => $data['product'],
                'section' => $data['Section'],
                'status' => $data['status'],
                'value_status' => 1,
                'payment_date' => $data['payment_date'],
                'note' => $data['note'],
                'user' => Auth::user()->name
            ]);

        } else {
            $invoice->update([
                'status' => $data['status'],
                'value_status' => 3,
                'payment_date' => $data['payment_date']
            ]);


            InvoiceDetails::create([
                'invoice_number' => $data['invoice_number'],
                'invoice_id' => $id,
                'product' => $data['product'],
                'section' => $data['Section'],
                'status' => $data['status'],
                'value_status' => 3,
                'payment_date' => $data['payment_date'],
                'note' => $data['note'],
                'user' => Auth::user()->name
            ]);
        }

        session()->flash("edit_status");
        return redirect(url('invoices'));
    }

    public function paidInvoices(){
        $invoices = Invoice::where("value_status" , 1)->get();
        return view('invoices.paid_invoices' , compact("invoices"));
    }

    public function UnpaidInvoices(){
        $invoices = Invoice::where("value_status" , 2)->get();
        return view('invoices.unpaid_invoices' , compact("invoices"));
    }

    public function PartialPaidInvoices(){
        $invoices = Invoice::where("value_status" , 3)->get();
        return view('invoices.partialPaid_invoices' , compact("invoices"));
    }

    public function printInvoice($id){
        $invoice = Invoice::FindOrFail($id);
        return view('invoices.print_invoice' , compact("invoice"));
    }

    public function readNotifications(){
        $notifications = auth()->user()->unreadNotifications;
        if ($notifications) {
            $notifications->markAsRead();
            return redirect()->back();
        }

    }

}
