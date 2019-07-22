<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        switch (true) {
            case currentUser()->hasRole('admin'):
                return view('admin.dashboard');
            case currentUser()->hasRole('client'):
                return view('home');
        }
    }
}
