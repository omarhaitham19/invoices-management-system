<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Section;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    function __construct()
    {
        $this->middleware(['permission:المنتجات'], ['only' => ['index', 'show']]);
        $this->middleware(['permission:اضافة منتج'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:تعديل منتج'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:حذف منتج'], ['only' => ['destroy']]);
    }



    public function index()
    {
        $sections = Section::all();
        $products = Product::all();
        return view('products.products' , compact("sections" , "products"));
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
        $data = $request->validate([
            "name"=>"string|required",
            "description"=>"string|nullable",
            "section_id"=>"required"
        ],[
            'name.required' =>'يرجي ادخال اسم المنتج',
            'name.unique' =>'اسم المنتج مسجل مسبقا',
            'section_id' => 'برجاء اختيار القسم'
        ]);


        Product::create([
            "name" => $request->name,
            "description" => $request->description,
            "section_id" => $request->section_id,
        ]);

        session()->flash("success" , "تم اضافة المنتج بنجاح");
        return redirect()->back();
    }
    

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $section_id = Section::where("section_name" , $request->section_name)->first()->id;
        $id = $request->id;

        $data = $request->validate([
            "name"=>"string|required",
            "description"=>"string|nullable",
        ],[
            'name.required' =>'يرجي ادخال اسم المنتج',
            'name.unique' =>'اسم المنتج مسجل مسبقا',
            'section_name' => 'برجاء اختيار القسم'
        ]);

        $product = Product::FindOrFail($id);
        $product->update([
            "name"=>$request->name,
            "description"=>$request->description,
            "section_id"=>$section_id
        ]);
        session()->flash("success", "تم تعديل المنتج بنجاح");
        return redirect()->back();
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->id;
        $product = Product::FindOrFail($id);
        $product->delete();
        session()->flash("success" , "تم حذف المنتج بنجاح");
        return redirect()->back();
    }
}

