<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Repositories\CollectorRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CollectorController extends Controller
{

    /**
     * @var CollectorRepository
     */
    private $collectorRepository;

    /**
     * CollectorController constructor.
     * @param CollectorRepository $collectorRepository
     */
    public function __construct(CollectorRepository $collectorRepository)
    {
        $this->middleware('auth');

        $this->collectorRepository = $collectorRepository;
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $collectors = $this->collectorRepository->getAll();
        return view('collector.create', compact('collectors'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function create(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'collector_name' => 'required',
            'nic_no' => ['nullable', 'regex:/^([1-9]{1}[0-9]{8}[vVxX])|([1-2]{1}[0-9]{11})$/'],
            'drivers_license_no' => 'nullable',
            'address' => 'nullable',
            'telephone_no' => ['nullable', 'regex:/^0[1-9]{1}[0-9]{8}$/'],
            'email_address' => 'required',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->route('collectors')->with('error', 'You have errors in your input. Please correct them and try again.')->withInput();
        }

        // create collector and user entries at the same time
        $createData = [
            'collector' => [
                'name' => $data['collector_name'],
                'address' => $data['address'],
                'telephone' => $data['telephone_no'],
                'nic_no' => $data['nic_no'],
                'drivers_license_no' => $data['drivers_license_no'],
            ],
            'user' => [
                'name' => $data['collector_name'],
                'email' => $data['email_address'],
                'password' => Hash::make($data['password']),
                'user_type' => UserType::COLLECTOR,
                'organization_id' => 1, // set to 'default organization' by default
            ],
        ];

        $collector = $this->collectorRepository->create($createData);
        if (empty($collector)) {
            return redirect()->route('collectors')->with('error', 'Failed to register the collector account. Please try again later.');
        } else {
            return redirect()->route('collectors')->with('success', 'Collector registered successfully.');
        }
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function edit(Request $request, $id)
    {
        $collector = $this->collectorRepository->getById($id);
        if (empty($collector)) {
            return redirect()->route('collectors')->with('error', 'Cannot find the requested collector profile.');
        }

        return view('collector.edit', compact('collector'));
    }

    public function update(Request $request, $id)
    {
        $collector = $this->collectorRepository->getById($id);
        if (empty($collector)) {
            return redirect()->route('collectors')->with('error', 'Cannot find the requested collector profile.');
        }

        $data = $request->all();

        $validator = Validator::make($data, [
            'collector_name' => 'required',
            'nic_no' => ['nullable', 'regex:/^([1-9]{1}[0-9]{8}[vVxX])|([1-2]{1}[0-9]{11})$/'],
            'drivers_license_no' => 'nullable',
            'address' => 'nullable',
            'telephone_no' => ['nullable', 'regex:/^0[1-9]{1}[0-9]{8}$/'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('collectors.edit')->with('error', 'You have errors in your input. Please correct them and try again.')->withInput();
        }

        $this->collectorRepository->update($id, [
            'name' => $data['collector_name'],
            'address' => $data['address'],
            'telephone' => $data['telephone_no'],
            'nic_no' => $data['nic_no'],
            'drivers_license_no' => $data['drivers_license_no'],
        ]);

        return redirect()->route('collectors')->with('success', 'Collector details updated successfully.');
    }

    public function updateUserAccount(Request $request, $id)
    {
        $collector = $this->collectorRepository->getById($id);
        if (empty($collector)) {
            return redirect()->route('collectors')->with('error', 'Cannot find the requested collector profile.');
        }

        $data = $request->all();

        $validator = Validator::make($data, [
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->route('collectors')->with('error', 'You have errors in your input. Please correct them and try again.');
        }

        $this->collectorRepository->updatePassword($id, [
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('collectors')->with('success', 'Collector user account updated successfully.');
    }

    public function delete(Request $request, $id)
    {
        $collector = $this->collectorRepository->getById($id);
        if (empty($collector)) {
            return redirect()->route('collectors')->with('error', 'Cannot find the requested collector profile.');
        }

        $this->collectorRepository->delete($id);

        return redirect()->route('collectors')->with('success', 'Collector user account updated successfully.');
    }

}
