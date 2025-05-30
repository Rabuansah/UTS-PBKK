<?php

namespace App\Http\Controllers;

use App\Models\BookAuthor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookAuthorController extends Controller
{
    public function index(): JsonResponse
    {
        $bookAuthors = BookAuthor::with(['book','author'])->get();
        return response()->json($bookAuthors, 200);
    }

    public function show($id): JsonResponse
    {
        try {
            $bookAuthor = BookAuthor::with(['book','author'])->findOrFail($id);
            return response()->json($bookAuthor, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Book Author tidak ditemukan'], 404);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'author_id' => 'required|exists:authors,id',
        ]);

        $bookAuthor = BookAuthor::create([
            'book_id' => $request->book_id,
            'author_id' => $request->author_id,
        ]);

        return response()->json([
            'message' => 'Book Author berhasil ditambahkan',
            'data' => $bookAuthor
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $bookAuthor = BookAuthor::findOrFail($id);

            $request->validate([
                'book_id' => 'sometimes|exists:books,id',
                'author_id' => 'sometimes|exists:authors,id',
            ]);

            // Only update the fields provided
            $data = $request->only(['book_id', 'author_id']);
            $bookAuthor->update($data);

            return response()->json([
                'message' => $bookAuthor->wasChanged()
                    ? 'Data Book Author berhasil diupdate'
                    : 'Tidak ada data yang diupdate',
                'data' => $bookAuthor
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Book Author tidak ditemukan'], 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $bookAuthor = BookAuthor::findOrFail($id);
            $bookAuthor->delete();

            return response()->json(['message' => 'Book Author berhasil dihapus']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Book Author tidak ditemukan'], 404);
        }
    }
}