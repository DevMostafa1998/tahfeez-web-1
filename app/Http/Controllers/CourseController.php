<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    /**
     * عرض قائمة الدورات.
     */
    public function index()
    {
        $courses = Course::oldest()->get();
        return view('courses.index', compact('courses'));
    }

    /**
     * تخزين دورة جديدة.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:courses,name',
        'type' => 'required|in:teachers,students,all', // أضفنا all هنا لكي يقبلها التقييم
    ]);

    Course::create([
        'name' => $validated['name'],
        // إذا كانت القيمة 'all' خزن null، وإلا خزن القيمة المختارة
        'type' => $validated['type'] === 'all' ? null : $validated['type'],
    ]);

    return redirect()->route('courses.index')->with('success', 'تم إضافة الدورة بنجاح');
}

public function update(Request $request, $id)
{
    $course = Course::findOrFail($id);

    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255', Rule::unique('courses')->ignore($course->id)],
        'type' => 'required|in:teachers,students,all',
    ]);
    // تحديد النوع الجديد
    $newType = $validated['type'] === 'all' ? null : $validated['type'];
    $oldType = $course->type;

    $course->update([
        'name' => $validated['name'],
        'type' => $newType,
    ]);
    if ($oldType !== $newType) {
        if ($newType === 'teachers') {
            $course->students()->detach();
        }
        elseif ($newType === 'students') {
            if (method_exists($course, 'users')) {
                $course->users()->detach();
            }
        }
    }
    return redirect()->route('courses.index')->with('success', 'تم تحديث الدورة بنجاح');
}

    /**
     * حذف الدورة.
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
