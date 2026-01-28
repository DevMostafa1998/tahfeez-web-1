<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BusinessLogic\ReportLogic;

class ReportController extends Controller
{
    protected $reportLogic;

    public function __construct(ReportLogic $logic)
    {
        $this->reportLogic = $logic;
    }

   public function index(Request $request)
{
    $filterLists = $this->reportLogic->getFilterLists();

    $memorizations = $this->reportLogic->getRecitationReport($request->all());

    if ($request->ajax()) {
        return response()->json($memorizations);
    }

    return view('reports.memorization', array_merge(
        ['memorizations' => $memorizations],
        $filterLists
    ));
}
}
