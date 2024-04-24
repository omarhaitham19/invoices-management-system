<?php

namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    function __construct()
    {
        $this->middleware(['permission:قائمة المستخدمين'], ['only' => ['index', 'show']]);
        $this->middleware(['permission:اضافة مستخدم'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:تعديل مستخدم'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:حذف مستخدم'], ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = User::latest()->paginate(5);
        return view('users.index',compact('data'));
    }

    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        return view('users.create', compact('roles'));        
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles_name' => 'required'
        ]);
    
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
    
        $user = User::create($input);
        $user->assignRole($request->input('roles_name'));
        session()->flash("add");
        return redirect()->route('users.index');
                        
    }
    
    public function show($id)
    {
        $user = User::FindOrFail($id);
        return view('users.show',compact('user'));
    }
    
    public function edit($id)
    {
        $user = User::FindOrFail($id);
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();
    
        return view('users.edit',compact('user','roles','userRole'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles_name' => 'required'
        ]);
    
        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }
    
        $user = User::FindOrFail($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();
    
        $user->assignRole($request->input('roles_name'));

        session()->flash("edit");
        return redirect()->route('users.index');
    }
    
    public function destroy(Request $request)
    {
       $id = $request->user_id;
       $user = User::FindOrFail($id);
       $user->delete();

       session()->flash("delete");
        return redirect()->route('users.index');
    }
}
