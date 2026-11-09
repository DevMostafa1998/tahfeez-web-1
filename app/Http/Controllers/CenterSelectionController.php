<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CenterSelectionController extends Controller
{
    public function setCenter(Request $request)
    {
        $request->validate([
            'center_id' => 'nullable|exists:centers,id'
        ]);

        if ($request->filled('center_id')) {
            session(['selected_center_id' => $request->center_id]);
        } else {
            session()->forget('selected_center_id'); 
        }

        return redirect()->back();
    }
}
