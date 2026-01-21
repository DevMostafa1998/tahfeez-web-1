<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BusinessLogic\MemorizationLogic;

class MemorizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $memorizationLogic;

    public function __construct(MemorizationLogic $logic)
    {
        $this->memorizationLogic = $logic;
    }
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'  => 'required|exists:student,id',
            'date'        => 'required|date',
            'sura_name'   => 'required|string|max:255',
            'verses_from' => 'required|integer|min:1',
            'verses_to'   => 'required|integer|gte:verses_from',
            'note'        => 'nullable|string',
        ]);

        $this->memorizationLogic->storeDailyMemorization($validated);

        return response()->json(['success' => true, 'message' => 'تم تسجيل الحفظ بنجاح']);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
