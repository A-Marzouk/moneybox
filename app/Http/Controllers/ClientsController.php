<?php

namespace App\Http\Controllers;

use App\classes\Notification;
use App\Client;
use App\Exports\ClientsExport;
use App\Product;
use App\Sale;
use App\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', '=', 'client');
        })->with('client')->get();

        return $users;
    }

    public function getCurrentClient()
    {
        return currentClient();
    }


    public function updateManager(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:191',
            'percentage' => 'required|max:191',
            'percentage_new_client' => 'max:191',
            'plan' => 'required|max:191',
        ]);

        $user = User::find($request->id);
        $client = $user->client;

        $user->update([
            'name' => $request->name
        ]);

        $client->update(
            [
                'percentage' => $request->percentage,
                'percentage_new_client' => $request->percentage_new_client,
                'plan' => $request->plan
            ]
        );

        $user['client'] = $client;
        return $user;

    }

    public function addManager(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|max:191',
            'email' => 'required|max:191|email|unique:users',
            'password' => 'required|max:191|min:6',
            'percentage' => 'required|max:191',
            'percentage_new_client' => 'max:191',
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
                'percentage_new_client' => $request->percentage_new_client,
                'plan' => $request->plan,
                'currency' => 'UAH',
            ],
        ]);

        $manager['client'] = $manager->client;

        return $manager;

    }

    public function deleteManager(Request $request)
    {
        return User::destroy($request->manager_id);
    }

    public function showEditProfilePage()
    {
        $user = currentUser();
        return view('client.edit_portfolio', compact('user'));
    }

    public function editPortfolio(Request $request)
    {

        $user = currentUser();
        $hasher = app('hash');
        if ($hasher->check($request->current_password, $user->password)) {
            $request->validate([
                'password' => 'min:7|required|confirmed',
            ]);


            $user->update([
                'password' => $request->password
            ]);

        } else {
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'current_password' => ['Неправильный пароль.']
            ]);
            throw $error;
        }

        return [
            'status' => 'success'
        ];

    }

    public function export()
    {
        Notification::productsAction('All Clients import');
        return Excel::download(new ClientsExport(), 'managers_list.xlsx');
    }


}
