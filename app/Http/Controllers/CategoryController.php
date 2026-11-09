<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Center;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
public function index()
{
    $centers = Center::all();

    $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
        ? session('selected_center_id')
        : null;

    $query = Category::with('center');
    if ($selectedCenterId) {
        $query->where('center_id', $selectedCenterId);
    }

    $categories = $query->latest()->get();

    return view('categories.index', compact('categories', 'centers', 'selectedCenterId'));
}

public function update(Request $request, $id)
{
    $category = Category::findOrFail($id);

    $validated = $request->validate([
        'name'      => 'required|string|max:255',
        'center_id' => 'nullable|exists:centers,id',
    ]);

    $centerId = !empty($validated['center_id']) && $validated['center_id'] !== 'null'
        ? $validated['center_id']
        : null;

    $category->update([
        'name'      => $validated['name'],
        'center_id' => $centerId,
    ]);

    return redirect()->back()->with('success', 'تم تحديث التصنيف والمركز بنجاح');
}

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'center_id' => 'nullable|exists:centers,id',
    ], [
        'name.required' => 'يرجى إدخال اسم التصنيف.',
        'center_id.exists' => 'المركز المحدد غير موجود.',
    ]);

    $exists = Category::where('name', $request->name)
        ->where(function ($query) use ($request) {
            if ($request->filled('center_id')) {
                $query->where('center_id', $request->center_id);
            } else {
                $query->whereNull('center_id');
            }
        })
        ->exists();

    if ($exists) {
        return redirect()->back()->with('error', 'هذا التصنيف موجود مسبقاً بنفس النطاق (عام أو لهذا المركز).');
    }

    Category::create([
        'name' => $request->name,
        'center_id' => $request->center_id,
    ]);

    return redirect()->back()->with('success', 'تم إضافة التصنيف بنجاح.');
}


    // حذف تصنيف
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        $usersCount = DB::table('user')->where('category_id', $id)->count();

        if ($usersCount > 0) {
            return redirect()->back()->with('error', 'عذراً، لا يمكن حذف هذا التصنيف لأنه مرتبط بـ (' . $usersCount . ') مستخدمين حالياً.');
        }

        $category->delete();
        return redirect()->back()->with('success', 'تم حذف التصنيف بنجاح.');
    }
}
