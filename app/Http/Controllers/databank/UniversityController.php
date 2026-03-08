<?php

namespace App\Http\Controllers\databank;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    public function index()
    {
        $universities = University::all();
        return view('project.universities.index', compact('universities'));
    }

    public function create()
    {
        return view('project.universities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:universities,name',
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('universities', 'public');
        }

        DB::table('universities')->insert([
            'name' => $request->name,
            'address' => $request->address,
            'email' => $request->email,
            'phone' => $request->phone,
            'logo' => $logoPath,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('universities.index')
                         ->with('success', 'University added successfully!');
    }

    public function edit(University $university)
    {
        return view('project.universities.edit', compact('university'));
    }

     public function update(Request $request, University $university)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:universities,name,' . $university->id,
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($university->logo) {
                Storage::disk('public')->delete($university->logo);
            }
            $university->logo = $request->file('logo')->store('universities', 'public');
        }

        $university->update([
            'name' => $request->name,
            'address' => $request->address,
            'email' => $request->email,
            'phone' => $request->phone,
            'logo' => $university->logo,
        ]);

        return redirect()->route('universities.index')
                         ->with('success', 'University updated successfully!');
    }

    // ----------------------
    // Delete University
    // ----------------------
    public function destroy(University $university)
    {
        // Delete logo file if exists
        if ($university->logo) {
            Storage::disk('public')->delete($university->logo);
        }

        $university->delete();

        return redirect()->route('universities.index')
                         ->with('success', 'University deleted successfully!');
    }
}
