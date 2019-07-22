<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        switch (true) {
            case $this->isAdmin():
                return redirect(route('admin.dashboard'));
            case $this->isClient():
                return  redirect(route('home'));
        }
    }

    protected function isAdmin(){
        return currentUser()->hasRole('admin');
    }

    protected function isClient(){
        return currentUser()->hasRole('client');
    }
}
