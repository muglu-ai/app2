<?php

namespace App\Http\Controllers;

use App\Models\DelOrgDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Exports\OrganizationsExport;
use Maatwebsite\Excel\Facades\Excel;

class DelOrgDetailController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = DelOrgDetail::withCount('delegates');

        if ($request->filled('pay_status')) {
            $query->where('pay_status', $request->pay_status);
        }
        if ($request->filled('sector')) {
            $query->where('sector', $request->sector);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('org_name', 'like', "%$search%")
                    ->orWhere('tin_no', 'like', "%$search%");
            });
        }

        $orgs = $query->orderByDesc('id')->paginate(10)->withQueryString();

        $sectors = DelOrgDetail::select('sector')->distinct()->pluck('sector');
        $statuses = DelOrgDetail::select('pay_status')->distinct()->pluck('pay_status');

        return view('delegate.index', compact('orgs', 'sectors', 'statuses'));
    }




    public function showDelegates(DelOrgDetail $organization)
    {
        $delegates = $organization->delegates;
        return view('delegate.delegates', compact('organization', 'delegates'));
    }
    public function show(DelOrgDetail $organization)
    {
        $organization->load('delegates');
        return view('delegate.show', compact('organization'));
    }

    public function export(Request $request)
    {
        $query = DelOrgDetail::with('delegates');

        if ($request->filled('pay_status')) {
            $query->where('pay_status', $request->pay_status);
        }

        if ($request->filled('sector')) {
            $query->where('sector', $request->sector);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('org_name', 'like', '%' . $request->search . '%')
                    ->orWhere('tin_no', 'like', '%' . $request->search . '%');
            });
        }

        $orgs = $query->get();

        return Excel::download(new OrganizationsExport($orgs), 'organizations_filtered.xlsx');
    }

}
