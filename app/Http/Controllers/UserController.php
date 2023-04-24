<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UserController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->middleware('auth');
        $this->userRepository = $userRepository;
    }

    /**
     * Show the user management page
     */
    public function index()
    {
        $users = $this->userRepository->list();
        return view('users.index', compact('users'));
    }

    public function create(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'user_type' => ['required'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.index')->withErrors()->withInput();
        }

        // register the user
        $user = $this->userRepository->create($data);
        // dd($user);

        if (empty($user)) {
            return redirect()->route('users.index')->with('error', 'Failed to create the user account. Please try again.');
        }

        return redirect()->route('users.index')->with('success', 'User registered successfully.');
    }

}
