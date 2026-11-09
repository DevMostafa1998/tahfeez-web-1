<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Center;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    /**
     * عرض قائمة الدورات المفلترة حسب المركز المحدد
     */

    public function index()
    {
        $centers = Center::all();

        $selectedCenterId = session()->has('selected_center_id') && session('selected_center_id') != ''
            ? session('selected_center_id')
            : null;

        $query = Course::with('center');

        if ($selectedCenterId) {
            $query->where('center_id', $selectedCenterId);
        }

        $courses = $query->latest()->get();

        return view('courses.index', compact('courses', 'centers'));
    }

    /**
     * تخزين دورة جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255|unique:courses,name',
            'type'      => 'required|in:teachers,students,all',
            'center_id' => 'nullable|exists:centers,id',
        ]);

        $centerId = !empty($validated['center_id']) && $validated['center_id'] !== 'null'
            ? $validated['center_id']
            : null;

        Course::create([
            'name'      => $validated['name'],
            'type'      => $validated['type'] === 'all' ? null : $validated['type'],
            'center_id' => $centerId,
        ]);

        return redirect()->route('courses.index')->with('success', 'تم إضافة الدورة بنجاح');
    }

    /**
     * تحديث بيانات الدورة
     */
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255', Rule::unique('courses')->ignore($course->id)],
            'type'      => 'required|in:teachers,students,all',
            'center_id' => 'nullable|exists:centers,id',
        ]);

        $newType = $validated['type'] === 'all' ? null : $validated['type'];
        $oldType = $course->type;

        $centerId = !empty($validated['center_id']) && $validated['center_id'] !== 'null'
            ? $validated['center_id']
            : null;

        $course->update([
            'name'      => $validated['name'],
            'type'      => $newType,
            'center_id' => $centerId,
        ]);

        if ($oldType !== $newType) {
            if ($newType === 'teachers') {
                if (method_exists($course, 'students')) {
                    $course->students()->detach();
                }
            } elseif ($newType === 'students') {
                if (method_exists($course, 'users')) {
                    $course->users()->detach();
                }
            }
        }

        return redirect()->route('courses.index')->with('success', 'تم تحديث الدورة بنجاح');
    }

    /**
     * حذف الدورة
     */
    public function destroy($id)
    {
        try {
            $course = Course::findOrFail($id);
            $course->delete();

            return redirect()->route('courses.index')->with('success', 'تم حذف الدورة بنجاح.');
        } catch (\Exception $e) {
            return redirect()->route('courses.index')->with('error', 'عذراً، لا يمكن حذف الدورة لارتباطها ببيانات أخرى.');
        }
    }
}
