<?php

namespace App\Http\Controllers;

use App\Models\SysUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function proseslogin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = SysUser::where('username', $request->username)->first();

        if (!$user) {
            return back()->with('error', 'Username tidak ditemukan');
        }

        // Jika password belum bcrypt
        if (!str_starts_with($user->password, '$2y$')) {
            // Upgrade password ke format Laravel
            $user->password = Hash::make($request->password);
            $user->save();
        }
        // Cek password pakai bcrypt
        elseif (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Password salah');
        }

        // Login user
        Auth::login($user);

        // Redirect ke dashboard
        return redirect()->route('main');
    }

    public function login()
    {
        return view('login.login');
    }


    public function dashboard()
    {
        $totalOngoing = DB::table('app_workflow as a')
            ->where('a.tipe', 'pr')
            ->where('a.processname', 'project_01')
            ->where('a.closing_project', '!=', '1')
            ->distinct('a.workflowid')       // pastikan hitung workflow unik
            ->count('a.workflowid');

        $user = Auth::user();
        return view('dashboard', compact('user', 'totalOngoing'));
    }

    public function logactivity()
    {
        $user = Auth::user();
        $username = $user->username;

        // Ambil semua log user login
        $logs = DB::table('tr_log_activity')
            ->select('*')  // ambil semua kolom
            ->where('username', $username)
            ->orderByDesc('date_proses')
            ->get();

        // Kelompokkan berdasarkan tanggal
        $timeline = $logs->groupBy(function ($item) {
            return Carbon::parse($item->date_proses)->format('d-m-Y');
        });

        return view('logactivity', compact('timeline', 'username'));
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah logout.');
    }
}
