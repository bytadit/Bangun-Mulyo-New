<?php

namespace App\Http\Controllers;

use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Carbon\Carbon;

use Alert;
use App\Models\Peminjam;

class PinjamanSingleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $peminjam_id = $request->peminjam_id;
        $periode = Pinjaman::where('peminjam_id', $peminjam_id)->count();
        $data = Pinjaman::create([
            'peminjam_id' => $peminjam_id,
            'tgl_pinjaman' => $request->tgl_pinjaman,
            'periode_pinjaman' => $periode + 1,
            'jangka_waktu' => 12,
            'tgl_jatuh_tempo' => Carbon::parse($request->tgl_pinjaman)->addMonths(12),
            'keperluan' => $request->keperluan,
            'keterangan' => $request->keterangan,
        ]);
        Alert::success('Sukses!', 'Data Pinjaman Perorangan berhasil diatur!');
        return redirect()->route('detail-single.index', ['single' => $peminjam_id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $single = $request->route('single');
        $pinjaman_single = $request->route('pinjaman_single');
        return view('dashboard.admin.detail-pinjaman-single.index', [
            'title' => 'Detail Pinjaman Periode ' . Pinjaman::where('id', $pinjaman_single)->first()->periode_pinjaman,
            'pinjaman_single' => $pinjaman_single,
            'single' => $single,
            'pinjaman' =>  Pinjaman::where('id', $pinjaman_single)->get(),
            'periode' => Pinjaman::where('id', $pinjaman_single)->first()->periode_pinjaman,
            'single_name' => Peminjam::where('id', $single)->first()->nama,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pinjaman_id = $request->pinjaman_id;
        $peminjam_id = $request->peminjam_id;
        $single = $request->route('single');
        Pinjaman::where('id', $pinjaman_id)
            ->update([
                'peminjam_id' => $peminjam_id,
                'tgl_pinjaman' => $request->etgl_pinjaman,
                'periode_pinjaman' => $request->eperiode,
                'jangka_waktu' => $request->ejangka_waktu,
                'tgl_jatuh_tempo' => Carbon::parse($request->etgl_pinjaman)->addMonths($request->ejangka_waktu),
                'keperluan' => $request->ekeperluan,
                'keterangan' => $request->eketerangan,
            ]);
        Alert::success('Sukses!', 'Data pinjaman perorangan berhasil diubah!');
        return redirect()->route('detail-single.index', ['single' => $peminjam_id]);
    }

    public function updateFull(Request $request, string $id)
    {
        $pinjaman_id = $request->pinjaman_id;
        $peminjam_id = $request->peminjam_id;
        $single = $request->route('single');
        Pinjaman::where('id', $pinjaman_id)
            ->update([
                'peminjam_id' => $peminjam_id,
                'tgl_pinjaman' => $request->etgl_pinjaman,
                'tgl_pencairan' => $request->etgl_pencairan,
                'tgl_pelunasan' => $request->etgl_pelunasan,
                'jumlah_pinjaman' => intval(str_replace('.', '', $request->ejumlah_pinjaman)),
                'jumlah_angsuran' => intval(str_replace('.', '', $request->ejumlah_angsuran)),
                'jumlah_iuran' => (1.3/100) * intval(str_replace('.', '', $request->ejumlah_pinjaman)),
                'jumlah_pokok' => intval(str_replace('.', '', $request->ejumlah_angsuran)) - ((1.3/100) * intval(str_replace('.', '', $request->ejumlah_pinjaman))),
                'periode_pinjaman' => $request->eperiode,
                'jangka_waktu' => $request->ejangka_waktu,
                'tgl_jatuh_tempo' => Carbon::parse($request->etgl_pinjaman)->addMonths($request->ejangka_waktu),
                'keperluan' => $request->ekeperluan,
                'keterangan' => $request->eketerangan,
            ]);
        Alert::success('Sukses!', 'Detail pinjaman perorangan berhasil diubah!');
        return redirect()->route('pinjaman-single.show', ['single' => $peminjam_id, 'pinjaman_single' => $pinjaman_id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $pinjaman_id = $request->pinjaman_id;
        $peminjam_id = $request->peminjam_id;
        Pinjaman::destroy($pinjaman_id);
        Alert::success('Sukses!', 'Data pinjaman perorangan berhasil dihapus!');
        return redirect()->route('detail-single.index', ['single' => $peminjam_id]);
    }
}
