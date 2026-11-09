<?php

namespace App\Http\Controllers;

use App\Models\Lov;
use App\Models\Center;
use Illuminate\Http\Request;

class LovController extends Controller
{
    public function index(Request $request)
    {
        $selectedCenterId = $request->get('center_id') ?? session('selected_center_id');
        $centers = Center::all();

        $lovsQuery = Lov::query();

        if ($selectedCenterId) {
            $lovsQuery->where('center_id', $selectedCenterId);
        }

        $lovs = $lovsQuery->orderBy('type')->get();

        return view('lov.index', compact('lovs', 'centers', 'selectedCenterId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:100',
            'description' => 'required|string|max:255',
            'center_id' => 'nullable|exists:centers,id',
        ]);

        Lov::create([
            'type' => $request->type,
            'description' => $request->description,
            'center_id' => $request->center_id,
            'is_valid' => $request->has('is_valid') ? true : false,
        ]);

        return redirect()->back()->with('success', 'تم إضافة الخيار بنجاح للجدول الموحد');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string|max:100',
            'description' => 'required|string|max:255',
            'center_id' => 'nullable|exists:centers,id',
        ]);

        $lov = Lov::findOrFail($id);
        $lov->update([
            'type' => $request->type,
            'description' => $request->description,
            'center_id' => $request->center_id,
            'is_valid' => $request->has('is_valid') ? true : false,
        ]);

        return redirect()->back()->with('success', 'تم تحديث البيانات بنجاح');
    }

    public function destroy($id)
    {
        $lov = Lov::findOrFail($id);
        $lov->delete();
        return response()->json(['success' => true]);
    }
}
