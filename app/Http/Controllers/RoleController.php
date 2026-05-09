<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\Role;
use Bouncer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $role = Role::whereNotIn('id',[1,2])->get();
        
        return view('role.index')->with('role',$role);
    }

    public function create()
    {
        return view('role.create');
    }

    public function store(Request $request)
    {   
        $role = Role::create($request->all());
        
        return redirect()->route('role.index')->withSuccess('Data saved');
    }

    public function edit(Role $role)
    {
        return view('role.create')->with('role',$role);
    }

    public function update(Request $request, Role $role)
    {
        // dd($request->all());
        $role->update($request->all());
        return redirect()->route('role.index')->withSuccess('Data updated');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('role.index')->withSuccess('Data deleted');
    }
   
}
