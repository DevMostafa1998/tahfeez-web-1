<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GenderSelectionController extends Controller
{
    public function setGender(Request $request)
    {
        $request->validate([
            'gender' => 'nullable|in:male,female'
        ]);

        if ($request->filled('gender')) {
            session(['selected_gender' => $request->gender]);
        } else {
            session()->forget('selected_gender');
        }

        return redirect()->back();
    }
}
