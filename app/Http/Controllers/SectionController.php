<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:الاقسام'], ['only' => ['index', 'show']]);
        $this->middleware(['permission:اضافة قسم'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:تعديل قسم'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:حذف قسم'], ['only' => ['destroy']]);
    }


    public function index()
    {
        $sections = Section::all();
        return view('sections.sections')->with("sections" , $sections);
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
            "section_name"=>"string|required|unique:sections",
            "description"=>"string|nullable"
        ],[
            'section_name.required' =>'يرجي ادخال اسم القسم',
            'section_name.unique' =>'اسم القسم مسجل مسبقا',
        ]);
       

        Section::create([
            "section_name" => $request->section_name,
            "description" => $request->description,
            "created_by" => Auth::user()->name
        ]);

        session()->flash("success" , "تم اضافة القسم بنجاح");
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Section $section)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Section $section)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
{
    $id = $request->id;
    $data = $request->validate([
        "section_name" => "required|string|unique:sections,section_name,$id",
        "description" => "nullable|string"
    ], [
        'section_name.required' => 'يرجى إدخال اسم القسم',
        'section_name.unique' => 'اسم القسم مسجل مسبقا',
        'description.required' => 'يرجى إدخال البيان',
    ]);

    $section = Section::findOrFail($id);
    $section->update([
        "section_name" => $request->section_name,
        "description" => $request->description,
        "created_by" => Auth::user()->name
    ]);

    session()->flash("success", "تم تعديل القسم بنجاح");
    return redirect()->back();

}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->id;
        $section = Section::findOrFail($id);
        $section->delete();
        session()->flash("success" , "تم حذف القسم بنجاح");
        return redirect()->back();
    }
}
