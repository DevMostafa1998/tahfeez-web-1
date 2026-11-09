<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Center;
use Illuminate\Support\Facades\Session;
class CenterController extends Controller
{
    public function index() {
        $centers = Center::all();
        return view('centers.index', compact('centers'));
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required|string|unique:centers,name']);
        \App\Models\Center::create(['name' => $request->name]);
        return redirect()->back()->with('success', 'تم إضافة المركز الجديد بنجاح');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:centers,name,' . $id
        ]);

        $center = Center::findOrFail($id);
        $center->update([
            'name' => $request->name
        ]);

        return redirect()->back()->with('success', 'تم تحديث اسم المركز بنجاح');
    }
    public function destroy($id)
        {
            $center = Center::findOrFail($id);

            if ($center->id == 1 || $center->name == 'أهل الرحمن') {
                return redirect()->back()->with('error', 'لا يمكن حذف المركز الرئيسي للمنظومة!');
            }
            $center->delete();

            return redirect()->route('centers.index')->with('success', 'تم حذف المركز بنجاح!');
        }
}
