<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsHifzExport;
use App\Imports\StudentsHifzImport;
use Illuminate\Support\Facades\Log;

class ExcelExportImportController extends Controller
{
    /**
     * عرض الصفحة الخاصة بالرفع والتحميل
     */
    public function showExcelPage($groupId)
    {
        $group = Group::findOrFail($groupId);
        return view('groups.excel-manage', compact('group'));
    }

    /**
     * دالة التصدير: تولد ملف إكسل يحتوي على طلاب المجموعة وأعمدة فارغة للحفظ
     */
    public function exportExcel($groupId)
{
    $group = Group::with('students')->findOrFail($groupId);

    $students = $group->students;

    if ($students->isEmpty()) {
        return back()->with('error', 'هذه المجموعة لا تحتوي على طلاب لتصديرهم.');
    }

    $groupName = $group->GroupName ?? $group->group_name ?? 'مجموعة_غير_معرفة';

    $safeGroupName = str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $groupName);

    $fileName = 'نموذج_حفظ_' . $safeGroupName . '_' . date('Y-m-d') . '.xlsx';

    return Excel::download(new StudentsHifzExport($students), $fileName);
}

   
        public function importExcel(Request $request)
{
    $request->validate([
        'excel_file' => 'required|mimes:xlsx,xls,csv',
        'group_id'   => 'required|exists:group,id' 
    ]);

    try {
        Excel::import(new \App\Imports\StudentsHifzImport, $request->file('excel_file'));

        if ($request->has('auto_export') && $request->auto_export == '1') {
            
            return back()
                ->with('success', 'تم استيراد البيانات بنجاح، جاري تنزيل الملف المحدث تلقائياً...')
                ->with('auto_download', route('excel.export', $request->group_id)); 
        }

        return back()->with('success', 'تم استيراد بيانات الحفظ بنجاح');

    } catch (\Exception $e) {
        return back()->with('error', 'خطأ في الاستيراد: ' . $e->getMessage());
    }
}
}
