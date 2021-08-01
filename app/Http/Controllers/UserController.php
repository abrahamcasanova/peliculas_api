<?php

namespace App\Http\Controllers;


use App\User;
use App\Role;
use App\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function show(User $user){

        $roles = $user->roles->pluck('name');
        $routes = $user->roles->first() ? $user->roles->first()->routes:[];
        return response()->json([
            'user'   => $user,
            'roles'  => $roles,
            'routes' => $routes
        ]);   
    }

    public function getAll(){
        return response()->json(User::with('roles')->get());  
    }

    public function getRoles(){
        $roles = Role::all();
        $roles = $roles->map(function ($role) {
            $role->key = $role->name;
            return $role;
        });

        return response()->json($roles);
    }

    public function update(Request $request){ 
        $request->validate([
            'name'  => 'required|string',
            'email' => 'required',
            'rol'   => 'required'
        ]);

        $user = User::find($request->id);
        $user->fill($request->all());

        if($request->password){
            $user->password = Hash::make($request->password);
        }
        $role_user = Role::where('key',$request->rol)->first();
        $user->roles()->detach();
        $user->roles()->attach($role_user);

        $user->save();
        return response()->json($user);
    }

    public function destroy(User $user){
        return response()->json($user->delete());
    }

    public function updatedRoles(Request $request){     
        $request->validate([
            'name' => 'required|string',
        ]);

        $role = Role::where('id',$request->id)->update([
            'routes' => json_encode($request->routes)
        ]);

        return response()->json($role);
    }

    public function storeRoles(Request $request){   
        $request->validate([
            'name'   => 'required|string',
            'routes' => 'required'
        ]);

        $request->merge(['key' => $request->name]);

        $role = Role::create(
            $request->all()
        );

        return response()->json($role);
    }


    public function deleteRole($key){
        $rol = Role::where('key',$key)->first();
        return response()->json($rol->delete());
    }

    public function showByIdAdminsyf(Request $request){
        $user =  User::where('id_adminsyf',$request->id_adminsyf)->first();
        return response()->json($user);
    }

    public function byEmailAdminsyf(Request $request){
        $user = User::where('email_adminsyf',$request->email)->get();
        return response()->json($user->pluck('id_adminsyf'));
    }  
    
    public function getFolderByIdAdminsyf(Request $request){
        $user_device = UserDevice::whereIn('id_adminsyf',$request->ids)->whereNotNull('folders')->first();
        return response()->json($user_device);
    } 

    public function store(Request $request){
        DB::beginTransaction();
            
        try {
            $user = User::create([
                'name'              => $request->name,
                'email'             => $request->email,
                'password'          => Hash::make($request->password),
            ]);
            $role_user = Role::where('key',$request->rol)->first();
    
            $user->roles()->attach($role_user);
            DB::commit();

            return response()->json(['message' => 'Usuario guardado correctamente!']);
        }
        catch (GlobalException $e) {
            DB::rollback();
            throw $e;
        }
    }
}