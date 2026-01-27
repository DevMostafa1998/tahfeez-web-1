<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
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
        $users = User::with('courses')->paginate(10);
        $categories = DB::table('categorie')->get();
        // جلب دورات المحفظين فقط
        // جلب الدورات التي تتبع المحفظين (teachers) أو التي تتبع الجميع (null)
        $all_courses = Course::where(function($query) {$query->where('type', 'teachers')->orWhereNull('type');
        })->get();

        return view('users.index', compact('users', 'categories', 'all_courses'));
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
            'courses'       => 'nullable|array',
        ]);

        $this->userLogic->storeUser($request->all());

        return redirect()->route('user')->with('success', 'تم إضافة المستخدم والدورات بنجاح!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'full_name'    => 'required|string|max:255',
            'id_number'    => 'required|unique:user,id_number,'.$id,
            'phone_number' => 'required',
            'address'      => 'required',
            'category_id'  => 'required',
            'password'     => 'nullable|string|min:6|confirmed',
            'courses'      => 'nullable|array',
        ]);

        $this->userLogic->updateUser($user, $request->all());

        return redirect()->route('user')->with('success', 'تم تحديث البيانات والدورات بنجاح');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->userLogic->deleteUser($user);
        return redirect()->route('user')->with('success', 'تم الحذف بنجاح');
    }
    public function create()
{
    // جلب التصنيفات
    $categories = DB::table('categorie')->get();

    //  جلب دورات المحفظين + الدورات العامة (null)
    $all_courses = Course::where(function($query) {
        $query->where('type', 'teachers')
              ->orWhereNull('type');
    })->get();

    return view('users.create', compact('categories', 'all_courses'));
}
}
