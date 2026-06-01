<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Kategori; 
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::all();
        return view('MasterData.kategori_index', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_kategori' => 'required|unique:kategoris,kode_kategori',
            'nama_kategori' => 'required',
            'jenis'         => 'required',
        ]);
        
        // PERBAIKAN: Definisikan kolomnya satu per satu secara eksplisit
        Kategori::create([
            'kode_kategori' => $request->kode_kategori,
            'nama_kategori' => $request->nama_kategori,
            'jenis'         => $request->jenis,
        ]);
        
        return redirect()->back()->with('success', 'Kategori Berhasil Ditambah');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori'   => 'required',
            'jenis'           => 'required',
        ]);

        $kategori = Kategori::findOrFail($id);
        
        $kategori->update([
            'nama_kategori'   => $request->nama_kategori,
            'jenis'           => $request->jenis,
        ]);

        return redirect()->back()->with('success', 'Data kategori berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();

        return redirect()->back()->with('success', 'Data kategori berhasil dihapus!');
    }
}