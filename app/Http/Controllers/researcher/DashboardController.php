<?php

namespace App\Http\Controllers\researcher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        return view('project.researcher.dashboard.dashboard');
    }
}
