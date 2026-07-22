<?php
namespace App\Traits;

use App\Http\Requests\User\AddValidation;
use App\Http\Requests\User\EditValidation;
use App\Models\Role;
use App\User;
use Illuminate\Http\Request;

trait UserScope{

    public function getUserNameId($id)
    {
        $user = User::find($id);

        if ($user) {
            return $user->name;
        }else{
            return "Unknown";
        }
    }

    public function getRoleByUserId($id)
    {
        $user = User::select('users.id', 'users.name', 'r.display_name')
            ->where('users.id',$id)
            ->join('role_user as ru','ru.user_id','=','users.id')
            ->join('roles as r','r.id','=','ru.role_id')
            ->first();

        if ($user) {
            return $user->display_name;
        }else{
            return "Unknown";
        }
    }

    public function getRoleNameId($id)
    {
        $role = Role::find($id);

        if ($role) {
            return $role->display_name;
        }else{
            return "Unknown";
        }
    }

    public function createUser(AddValidation $request)
    {
        // dd($request->all());
        if($request->password != $request->confirmPassword){
            $request->session()->flash($this->message_warning, 'Password & Confirm Password Not Match.');
            return redirect()->back();
        }

        $request->request->add(['password' => bcrypt($request->get('password'))]);
        $request->request->add(['hook_id' => isset($request->hook_id)?intval(decrypt($request->get('hook_id'))):'']);
        $request->request->add(['role_id' => isset($request->role_id)?intval($request->get('role_id')):'']);
        /*A freshly created login should be active so the user can log in.*/
        $request->request->add(['status' => 'active']);

        //dd($request->all());
        $user = User::create($request->all());

        $roles = [];
        $roles[] = [
            'user_id' => $user->id,
            'role_id' => intval($request->role_id)
        ];

        $user->userRole()->sync($roles);

        $request->session()->flash($this->message_success, 'Create Login Detail Successfully.');
        return redirect()->back();
    }


//    public function createUser(AddValidation $request)
//    {
//        if($request->password != $request->confirmPassword){
//            $request->session()->flash($this->message_warning, 'Password & Confirm Password Not Match.');
//            return redirect()->back();
//        }
//
//        $request->request->add(['password' => bcrypt($request->get('password'))]);
//
//        $user = User::create($request->all());
//
//        $roles = [];
//        $roles[] = [
//            'user_id' => $user->id,
//            'role_id' => $request->role_id
//        ];
//
//        $user->userRole()->sync($roles);
//
//        $request->session()->flash($this->message_success, 'Create Login Detail Successfully.');
//        return redirect()->back();
//    }

    public function updateUser(EditValidation $request, $id)
    {
        $id = decrypt($id);
        if (!$row = User::find($id)) return parent::invalidRequest();

        if($request->password != $request->confirmPassword){
            $request->session()->flash($this->message_warning, 'Password & Confirm Password Not Match.');
            return redirect()->back();
        }

        if ($request->get('password')){
            $new_password= bcrypt($request->get('password'));
            /*Setting a login password activates the user so they can log in
              (the login check requires status = 1). Without this an online-
              registered student stays "in-active" in the users table even after
              a password is assigned, and login fails with "credentials do not match".*/
            $request->request->add(['status' => 'active']);
        }

        $request->request->add(['password' => isset($new_password)?$new_password:$row->password]);
        $request->request->add(['hook_id' => decrypt($request->hook_id)]);

        $row->update($request->all());

        $roles = [];
        $roles[] = [
            'user_id' => $row->id,
            'role_id' => $request->role_id
        ];

        $row->userRole()->sync($roles);

        $request->session()->flash($this->message_success, 'Login Detail Updated Successfully.');
        return redirect()->back();
    }

    public function deleteUser(Request $request, $id)
    {
        if (!$row = User::find($id)) return parent::invalidRequest();

        $row->delete();

        $roles = [];
        if($request->get('role')){
            foreach ($request->get('role') as $key => $role){
                $roles[$key] = [
                    'user_id' => $row->id,
                    'role_id' => $role
                ];
            }
        }

        $row->userRole()->sync($roles);

        $request->session()->flash($this->message_success,'Login Access Deleted Successfully.');
        return redirect()->back();

    }

    public function activeUser(request $request, $id)
    {
        if (!$row = User::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, 'User Un-Locked Successfully.');
        return redirect()->back();
    }

    public function inActiveUser(request $request, $id)
    {
        if (!$row = User::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, 'User Locked Successfully.');
        return redirect()->back();
    }

}