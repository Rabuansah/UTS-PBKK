<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LoanController extends Controller
{
    public function index(): JsonResponse
    {
        $loans = Loan::with(['user','book'])->get();
        return response()->json($loans, 200);
    }

    public function show($id): JsonResponse
    {
        try {
            $loan = Loan::with(['user','book'])->findOrFail($id);
            return response()->json($loan, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data peminjaman tidak ditemukan'], 404);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
        ]);

        $loan = Loan::create([
            'user_id' => $request->user_id,
            'book_id' => $request->book_id,
        ]);

        return response()->json([
            'message' => 'Data peminjaman berhasil ditambahakn',
            'data' => $loan
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $loan = Loan::findOrFail($id);

            $request->validate([
                'user_id' => 'sometimes|exists:users,id',
                'book_id' => 'sometimes|exists:books,id',
            ]);

            // Only update the fields provided
            $data = $request->only(['user_id', 'book_id']);
            $loan->update($data);

            return response()->json([
                'message' => $loan->wasChanged()
                    ? 'Data peminjaman berhasil diupdate'
                    : 'Tidak ada data yang diupdate',
                'data' => $loan
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data peminjaman tidak ditemukan'], 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $loan = Loan::findOrFail($id);
            $loan->delete();

            return response()->json(['message' => 'Data peminjaman berhasil dihapus']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data peminjaman tidak ditemukan'], 404);
        }
    }
}