<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceReportController extends Controller
{

    function __construct()
    {
        $this->middleware(['permission:تقرير الفواتير'], ['only' => ['index' , 'searchInvoices']]);
    }


    public function index(){
        return view('reports.invoices_report');
    }

    public function searchInvoices(Request $request){
        $radio = $request->radio;

        if ($radio == "1") {
            $type = $request->type;

            if ($type && $request->start_at == "" && $request->end_at == "") {
                $invoices = Invoice::where("status" , $type)->get();
                return view('reports.invoices_report' , compact("invoices" , "type"));

            }else{

                if ($request->has("end_at")) {
                    $end_at = date($request->end_at);
                }else{
                    $end_at = date('Y-m-d');
                }
                $start_at = date($request->start_at);
                $type = $request->type;
                $invoices = Invoice::whereBetween("invoice_date" , [$start_at , $end_at])->where("status" , $type)->get();
                return view('reports.invoices_report' , compact("invoices" , "type"));
            }


        } else {
            $invoice_number = $request->invoice_number;
            $invoices = Invoice::where("invoice_number" , $invoice_number)->get();
            return view('reports.invoices_report')->with("invoices" , $invoices);
        }

    }
}
