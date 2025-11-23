<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyStoreRequest;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function index()
    {
        return Inertia::render('Company/Index');
    }

    public function create()
    {
        return Inertia::render('Company/Create');
    }

    public function store(CompanyStoreRequest $request)
    {
        $logo = null;

        if ($request->file('logo')) {
            $logo = $request->file('logo')->store('company', ['disk' => 'public']);
        }

        Company::create([
            'name' => $request->name,
            'email' => $request->email,
            'logo' => $logo,
            'website' => $request->website,
        ]);

        return redirect()->route('company.index');
    }

    public function list(Request $request)
    {
        $query = Company::query();

        // Sort
        if ($request->sortField && $request->sortOrder) {
            $field = $request->sortField;
            $order = $request->sortOrder === 'ascend' ? 'asc' : 'desc';

            $query->orderBy($field, $order);
        }

        // Pagination
        $perPage = $request->results ? $request->results : 10;

        $companies = $query->paginate($perPage);

        return json_encode([
            'results' => $companies->items(),
            'total' => $companies->total(),
        ]);
    }

    public function update(CompanyStoreRequest $request, Company $company)
    {
        if ($request->file('logo')) {
            try {
                Storage::disk('public')->delete($company->logo);
            } catch (\Throwable $th) {}

            $logo = $request->file('logo')->store('company', ['disk' => 'public']);

            $company->logo = $logo;
        }

        $company->name = $request->name;
        $company->email = $request->email;
        $company->website = $request->website;

        $company->save();

        return redirect()->route('company.index');
    }

    public function destroy(Company $company)
    {
        try {
            Storage::disk('public')->delete($company->logo);
        } catch (\Throwable $th) {}

        $company->delete();

        return redirect()->route('company.index');
    }
}
