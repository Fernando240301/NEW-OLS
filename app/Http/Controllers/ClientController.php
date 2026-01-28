<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index()
    {
        // sementara dummy data
        $data = DB::table('pemohon')
            ->leftJoin(
                'ref_klasifikasi_client',
                'pemohon.klasifikasi',
                '=',
                'ref_klasifikasi_client.id'
            )
            ->select(
                'pemohon.*',
                'ref_klasifikasi_client.nama'
            )
            ->orderByDesc('pemohonid')
            ->get();

        return view('client.index', compact('data'));
    }

    //Add Client
    public function create()
    {
        $klasifikasi = DB::table('ref_klasifikasi_client')
            ->orderBy('nama')
            ->get();

        return view('client.create', compact('klasifikasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'klasifikasi' => 'required',
            'email_pemohon' => 'required',
        ]);

        // 1️⃣ Ambil inisial dari nama perusahaan
        $words = explode(' ', trim($request->nama_perusahaan));
        $inisial = '';

        foreach ($words as $word) {
            $inisial .= strtoupper(substr($word, 0, 1));
        }

        // 2️⃣ Tambah angka random (4 digit)
        $randomNumber = rand(1000, 9999);

        // 3️⃣ Gabungkan jadi NIK
        $nik = $inisial . '-' . $randomNumber;

        DB::table('pemohon')->insert([
            'nik' => $nik,
            'nama_perusahaan' => $request->nama_perusahaan,
            'klasifikasi' => $request->klasifikasi,
            'email_pemohon' => $request->email_pemohon,
            'alamat_perusahaan' => $request->alamat_perusahaan,
            'kota_perusahaan' => $request->kota_perusahaan,
            'provinsi_perusahaan' => $request->provinsi_perusahaan,
            'negara' => $request->negara,
            'kode_pos' => $request->kode_pos,
            'telp_perusahaan' => $request->telp_perusahaan,
            'contact1' => $request->contact1,
            'contact_celluler1' => $request->contact_celluler1,
            'contact2' => $request->contact2,
            'contact_celluler2' => $request->contact_celluler2,
            'contact3' => $request->contact3,
            'contact_celluler3' => $request->contact_celluler3,
        ]);

        return redirect()
            ->route('client.index')
            ->with('success', 'Data client berhasil ditambahkan');
    }

    //Edit Client
    public function edit($id)
    {
        $client = DB::table('pemohon')
            ->where('pemohonid', $id)
            ->first();

        if (!$client) {
            abort(404);
        }

        $klasifikasi = DB::table('ref_klasifikasi_client')
            ->orderBy('nama')
            ->get();

        return view('client.edit', compact('client', 'klasifikasi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'klasifikasi'     => 'required',
            'email_pemohon'   => 'required|email',
        ]);

        DB::table('pemohon')
            ->where('pemohonid', $id)
            ->update([
                'nama_perusahaan'     => $request->nama_perusahaan,
                'klasifikasi'         => $request->klasifikasi,
                'email_pemohon'       => $request->email_pemohon,
                'alamat_perusahaan'   => $request->alamat_perusahaan,
                'kota_perusahaan'     => $request->kota_perusahaan,
                'provinsi_perusahaan' => $request->provinsi_perusahaan,
                'negara'              => $request->negara,
                'kode_pos'            => $request->kode_pos,
                'telp_perusahaan'     => $request->telp_perusahaan,
                'contact1'            => $request->contact1,
                'contact_celluler1'   => $request->contact_celluler1,
                'contact2'            => $request->contact2,
                'contact_celluler2'   => $request->contact_celluler2,
                'contact3'            => $request->contact3,
                'contact_celluler3'   => $request->contact_celluler3,
            ]);

        return redirect()
            ->route('client.index')
            ->with('success', 'Data client berhasil diperbarui');
    }

    // Delete Client
    public function delete($id)
    {
        $deleted = DB::table('pemohon')
            ->where('pemohonid', $id)
            ->delete();

        if (!$deleted) {
            return redirect()
                ->route('client.index')
                ->with('error', 'Data client tidak ditemukan');
        }

        return redirect()
            ->route('client.index')
            ->with('success', 'Data client berhasil dihapus');
    }
}
