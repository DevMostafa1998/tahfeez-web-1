<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\BusinessLogic\UserLogic;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected $userLogic;

    public function __construct(UserLogic $userLogic)
    {
        $this->userLogic = $userLogic;
    }

    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $categories = DB::table('categorie')->get();
        return view('users.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name'     => 'required|string|max:255',
            'id_number'     => 'required|unique:user,id_number',
            'password'      => 'required|string|min:6|confirmed',
            'date_of_birth' => 'required|date',
            'phone_number'  => 'required',
            'address'       => 'required',
            'category_id'   => 'required',
        ]);

        $this->userLogic->storeUser($request->all());

        return redirect()->route('user')->with('success', 'تم إضافة المستخدم بنجاح!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'id_number' => 'required|unique:user,id_number,'.$id,
            'phone_number' => 'required',
            'address' => 'required',
            'category_id' => 'required',
        ]);

        $this->userLogic->updateUser($user, $request->all());

        return redirect()->route('user')->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->userLogic->deleteUser($user);

        return redirect()->route('user')->with('success', 'تم نقل المستخدم إلى المحذوفات بنجاح');
    }
}
