<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    // حفظ تصنيف جديد
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categorie,name',
        ], [
            'name.unique' => 'هذا التصنيف موجود مسبقاً.'
        ]);

        Category::create([
            'name' => $request->name
        ]);

        return redirect()->back()->with('success', 'تم إضافة التصنيف بنجاح.');
    }

    // تحديث تصنيف موجود
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categorie,name,' . $id,
        ]);

        $category->update([
            'name' => $request->name
        ]);

        return redirect()->back()->with('success', 'تم تحديث التصنيف بنجاح.');
    }

    // حذف تصنيف
    public function destroy($id)
{
    $category = Category::findOrFail($id);

    // التحقق هل يوجد مستخدمين مرتبطين بهذا التصنيف؟
    $usersCount = DB::table('user')->where('category_id', $id)->count();

    if ($usersCount > 0) {
        return redirect()->back()->with('error', 'عذراً، لا يمكن حذف هذا التصنيف لأنه مرتبط بـ (' . $usersCount . ') مستخدمين حالياً. قم بتغيير تصنيفاتهم أولاً.');
    }

    $category->delete();
    return redirect()->back()->with('success', 'تم حذف التصنيف بنجاح.');
}
}
