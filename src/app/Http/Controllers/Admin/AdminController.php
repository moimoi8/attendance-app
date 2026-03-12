<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Attendance;

class AdminController extends Controller
{
  public function index(Request $request)
  {
    $date = $request->query('date', Carbon::today()->format('Y-m-d'));
    $attendances = Attendance::with('user')->where('date', $date)->get();

    return view('admin.attendance.daily', compact('date', 'attendances'));
  }

  public function staffList()
  {
    return view('admin.staff.list');
  }

  public function approveList()
  {
    return view('admin.approve.list');
  }

  public function show($id)
  {
    $attendance = Attendance::with(['user', 'rests'])->findOrFail($id);

    if (!$attendance) {
      abort(404);
    }

    return view('admin.attendance.detail', compact('attendance'));
  }
}
