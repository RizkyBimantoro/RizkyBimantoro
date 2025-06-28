<?php

namespace App\Http\Controllers;

use App\Http\Resources\MahasiswaResource;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MahasiswaController extends Controller
{
    public function index()
    {
        $mahasiswa = Mahasiswa::all();
        return MahasiswaResource::collection($mahasiswa);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nim' => 'required|string|max:12|unique:mahasiswas,nim',
            'nama' => 'required|string|max:225',
            'jk' => 'required|string|max:1',
            'tgl_lahir' => 'required|date',
            'jurusan' => 'required|string|max:100',
            'alamat' => 'required|string|max:255'
        ]);

        $mahasiswa = Mahasiswa::create($request->all());

        return (new MahasiswaResource($mahasiswa))
            ->additional([
                'success' => true,
                'message' => 'Mahasiswa created successfully'
            ]);
    }

    // PERBAIKAN: Cari berdasarkan NIM, bukan ID
    public function show(string $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();
        return (new MahasiswaResource($mahasiswa))
            ->additional([
                'success' => true,
                'message' => 'Data Mahasiswa retrieved successfully'
            ]);
    }

    // PERBAIKAN: Update berdasarkan NIM, bukan ID
    public function update(Request $request, string $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();

        $request->validate([
            'nim' => [
                'required',
                'string',
                'max:12',
                // PERBAIKAN: Ignore berdasarkan NIM yang sedang diedit
                Rule::unique('mahasiswas', 'nim')->ignore($nim, 'nim')
            ],
            'nama' => 'required|string|max:225',
            'jk' => 'required|string|max:1',
            'tgl_lahir' => 'required|date',
            'jurusan' => 'required|string|max:100',
            'alamat' => 'required|string|max:255'
        ]);

        $mahasiswa->update($request->all());

        return (new MahasiswaResource($mahasiswa))
            ->additional([
                'success' => true,
                'message' => 'Mahasiswa updated successfully'
            ]);
    }

    // PERBAIKAN: Delete berdasarkan NIM, bukan ID
    public function destroy(string $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();
        $mahasiswa->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mahasiswa deleted successfully'
        ]);
    }
}