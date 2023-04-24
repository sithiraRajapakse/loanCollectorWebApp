<?php

namespace App\Http\Controllers;

use App\Enums\SchemeType;
use App\Repositories\SchemeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SchemeController extends Controller
{
    /**
     * @var SchemeRepository
     */
    private $schemeRepository;

    public function __construct(SchemeRepository $schemeRepository)
    {
        $this->middleware('auth');
        $this->schemeRepository = $schemeRepository;
    }

    public function index(Request $request)
    {
        $schemes = $this->schemeRepository->getAll();

        return view('loan-schemes.create', compact('schemes'));
    }

    public function create(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'title' => 'required',
            'type' => ['required', Rule::in(SchemeType::DAILY, SchemeType::WEEKLY, SchemeType::MONTHLY, SchemeType::CUSTOM)],
            'interest_rate' => 'required|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->route('loans.schemes')->withInput()->withErrors()->with('error', 'You have errors in your input. Please check and correct them before submitting again.');
        }

        $scheme = $this->schemeRepository->create($data);
        if (empty($scheme)) {
            return redirect()->route('loans.schemes')->with('error', 'Failed to create loan scheme. Please try again.');
        }

        return redirect()->route('loans.schemes')->with('success', 'Successfully created the loan scheme.');
    }

    public function edit(Request $request, $id)
    {
        $scheme = $this->schemeRepository->getById($id);
        if (empty($scheme)) {
            return redirect()->route('loans.schemes')->with('error', 'Cannot find the loan scheme.');
        }

        return view('loan-schemes.edit', compact('scheme'));
    }

    public function update(Request $request, $id)
    {
        $scheme = $this->schemeRepository->getById($id);
        if (empty($scheme)) {
            return redirect()->route('loans.schemes')->with('error', 'Cannot find the loan scheme.');
        }
        $data = $request->all();

        $validator = Validator::make($data, [
            'title' => 'required',
            'type' => ['required', Rule::in(SchemeType::DAILY, SchemeType::WEEKLY, SchemeType::MONTHLY, SchemeType::BI_WEEKLY)],
            'interest_rate' => 'required|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->route('loans.schemes.edit', $id)->withInput()->withErrors($validator)->with('error', 'You have errors in your input. Please check and correct them before submitting again.');
        }

        $this->schemeRepository->update($id, $data);
        return redirect()->route('loans.schemes')->with('success', 'Successfully updated the loan scheme.');
    }

    public function delete(Request $request, $id)
    {
        $scheme = $this->schemeRepository->getById($id);
        if (empty($scheme)) {
            return redirect()->route('loans.schemes')->with('error', 'Cannot find the loan scheme.');
        }

        $this->schemeRepository->delete($id);

        return redirect()->route('loans.schemes')->with('success', 'Successfully deleted the loan scheme.');
    }

    public function getSchemeDetailsByIdJson(Request $request)
    {
        $id = $request->post('id');

        $scheme = $this->schemeRepository->getById($id);
        return response()->json($scheme);
    }

}
