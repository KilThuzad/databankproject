<?php

namespace App\Http\Controllers\databank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\MemberAgency;


class AgencyController extends Controller
{
    public function index()
    {
        $memberAgencies = MemberAgency::all();
        return view('project.agency.index', compact('memberAgencies'));
    }

    public function create()
    {
        return view('project.agency.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255|unique:member_agencies,name',
            'address' => 'nullable|string|max:255',
            'email'   => 'nullable|email|max:255',
            'logo'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('member-agencies', 'public');
        }

        DB::table('member_agencies')->insert([
            'name'       => $request->name,
            'address'    => $request->address,
            'email'      => $request->email,
            'logo'       => $logoPath,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('member-agencies.index')
                         ->with('success', 'Member agency added successfully!');
    }

    public function edit(MemberAgency $memberAgency)
    {
        $agency = $memberAgency; // alias
        return view('project.agency.edit', compact('agency'));
    }


    public function update(Request $request, MemberAgency $memberAgency)
    {
        $request->validate([
            'name'    => 'required|string|max:255|unique:member_agencies,name,' . $memberAgency->id,
            'address' => 'nullable|string|max:255',
            'email'   => 'nullable|email|max:255',
            'logo'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($memberAgency->logo) {
                Storage::disk('public')->delete($memberAgency->logo);
            }
            $memberAgency->logo = $request->file('logo')->store('member-agencies', 'public');
        }

        $memberAgency->update([
            'name'    => $request->name,
            'address' => $request->address,
            'email'   => $request->email,
            'logo'    => $memberAgency->logo,
        ]);

        return redirect()->route('member-agencies.index')
                         ->with('success', 'Member agency updated successfully!');
    }

    public function destroy(MemberAgency $memberAgency)
    {
        if ($memberAgency->logo) {
            Storage::disk('public')->delete($memberAgency->logo);
        }

        $memberAgency->delete();

        return redirect()->route('member-agencies.index')
                         ->with('success', 'Member agency deleted successfully!');
    }
}
