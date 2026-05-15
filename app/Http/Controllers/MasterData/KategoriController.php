<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Kategori; // Pastikan model Kategori di-import
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::all();
        return view('MasterData.kategori_index', compact('kategori'));
    }

    // TAMBAHKAN METHOD STORE INI
    public function store(Request $request)
    {
        $request->validate([
            'kode_kategori' => 'required|unique:kategoris,kode_kategori',
            'nama_kategori' => 'required',
            'tahun_pengadaan' => 'required',
            'merk' => 'required',
            'jenis' => 'required',
        ]);
    
        // Menggunakan create untuk menyimpan data
        Kategori::create($request->all());
    
        return redirect()->back()->with('success', 'Kategori Berhasil Ditambah');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori'   => 'required',
            'tahun_pengadaan' => 'required|numeric',
            'merk'            => 'required',
            'jenis'           => 'required',
        ]);

        $kategori = Kategori::findOrFail($id);
        
        $kategori->update([
            'nama_kategori'   => $request->nama_kategori,
            'tahun_pengadaan' => $request->tahun_pengadaan,
            'merk'            => $request->merk,
            'jenis'           => $request->jenis,
        ]);

        return redirect()->back()->with('success', 'Data kategori berhasil diperbarui!');
    }

    /**
     * Hapus data kategori.
     */
    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();

        return redirect()->back()->with('success', 'Data kategori berhasil dihapus!');
    }

}