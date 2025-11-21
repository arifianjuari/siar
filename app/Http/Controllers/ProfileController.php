<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\WorkUnit;

class ProfileController extends Controller
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
     * Show the form for editing the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = Auth::user();
        $tenantId = session('tenant_id');

        // Ambil daftar unit kerja yang aktif dalam tenant yang sama
        $workUnits = WorkUnit::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('unit_name')
            ->get();

        return view('profile.edit', compact('user', 'workUnits'));
    }

    /**
     * Update the user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $tenantId = session('tenant_id');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'position' => 'nullable|string|max:255',
            'rank' => 'nullable|string|max:255',
            'nrp' => 'nullable|string|max:255',
            'work_unit_id' => 'nullable|exists:work_units,id',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Cek apakah work_unit_id valid untuk tenant yang sama
        if ($request->filled('work_unit_id')) {
            $workUnit = WorkUnit::find($request->work_unit_id);
            if (!$workUnit || $workUnit->tenant_id != $tenantId) {
                return back()->withErrors(['work_unit_id' => 'Unit kerja tidak valid.']);
            }
        }

        // Update user info
        $user->name = $request->name;
        $user->email = $request->email;
        $user->position = $request->position;
        $user->rank = $request->rank;
        $user->nrp = $request->nrp;
        $user->work_unit_id = $request->work_unit_id;

        // Update password if provided
        if ($request->filled('password')) {
            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
            }

            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update user's profile photo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::user();

        // Hapus foto lama jika ada
        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        // Upload foto baru
        $path = $request->file('profile_photo')->store('profile-photos', 'public');
        $user->profile_photo = $path;
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Foto profil berhasil diperbarui!');
    }

    /**
     * Remove user's profile photo.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removePhoto()
    {
        $user = Auth::user();

        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->profile_photo = null;
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Foto profil berhasil dihapus!');
    }
}
