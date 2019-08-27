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
       $users =  User::whereHas('roles', function ($query) {
            $query->where('name', '=', 'client');
        })->with('client')->get();

       return $users ;
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
            'email' => 'required|max:191|email|unique:users',
            'password' => 'required|max:191|min:6',
            'percentage' => 'required|max:191',
            'plan' => 'required|max:191',
        ]);


        $manager = app(User::class)->createClient([
            'user' => [
                'email' => $request->email,
                'password' => $request->password,
                'plain_password' => $request->password,
                'username' => 'client',
                'name' => $request->name,
            ],
            'client' => [
                'percentage' => $request->percentage,
                'plan' => $request->plan,
                'currency' => 'UAH',
            ],
        ]);

        $manager['client'] = $manager->client;

        return $manager ;

    }

    public function deleteManager(Request $request){
       return User::destroy($request->manager_id);
    }




}
