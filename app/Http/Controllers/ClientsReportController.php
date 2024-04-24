<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Section;
use Illuminate\Http\Request;

class ClientsReportController extends Controller
{

    function __construct()
    {
        $this->middleware(['permission:تقرير العملاء'], ['only' => ['index', 'searchClients']]);
    }


    public function index(){
        $sections = Section::all();
        return view('reports.clients_report' , compact("sections"));
    }

    public function searchClients(Request $request){

        $this->validate($request , [
            "Section"=>"required|string"
        ],[
            "Section.required" => "برجاء اختيار القسم"
        ]);

        $section = $request->Section;
        $product = $request->product;
        $startAt = $request->start_at;
        $endAt = $request->end_at;

        if ($section && $product && $startAt == "" && $endAt == "") {
            $invoices = Invoice::where("section_id" , $section)->where("product" , $product)->get();
            $sections = Section::all();
            return view('reports.clients_report' , compact("invoices" , "sections"));
        } else {
            $start_at = date($request->start_at);
            $end_at = date($request->end_at);

            $invoices = Invoice::whereBetween("invoice_date" , [$start_at , $end_at])->where("section_id" , $section)->where("product" , $product)->get();
            $sections = Section::all();
            return view('reports.clients_report' , compact("invoices" , "sections"));

    }
}
}