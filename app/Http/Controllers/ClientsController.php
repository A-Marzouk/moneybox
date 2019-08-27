<?php

namespace App\Http\Controllers;

use App\Client;
use App\User;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return array of objects
     */
    public function getManagers()
    {
       return  User::whereHas('roles', function ($query) {
            $query->where('name', '=', 'client');
        })->with('client')->get();

    }

    public function getCurrentClient()
    {
       return  currentClient() ;
    }


    public function updateManager(Request $request){
        $this->validate($request, [
            'name' => 'required|max:191',
            'percentage' => 'required|max:191',
            'plan' => 'required|max:191',
        ]);

        $user   = User::find($request->id);
        $client = $user->client;

        $user->update([
            'name' => $request->name
        ]);

        $client->update(
            [
                'percentage' => $request->percentage,
                'plan' => $request->plan
            ]
        );

        $user['client'] = $client;
        return $user ;

    }

    public function addManager(Request $request){
        $this->validate($request, [
            'name' => 'required|max:191',
            'percentage' => 'required|max:191',
            'plan' => 'required|max:191',
        ]);

        return app(User::class)->createClient([
            'user' => [
                'email' => 'client@moneybox.com',
                'password' => '123456789',
                'username' => 'client',
                'name' => 'client 1',
            ],
            'client' => [
                'percentage' => 5,
                'plan' => 500,
                'currency' => 'UAH',
            ],
        ]);

    }

    public function deleteManager(Request $request){
        return Client::destroy($request->manager_id);
    }




}
