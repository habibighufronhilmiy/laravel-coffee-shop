<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json($request->user()->addresses()->latest()->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'label' => 'nullable|string|max:50',
            'alamat' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'penerima' => 'nullable|string|max:100',
            'no_telp_penerima' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        $validated['user_id'] = $request->user()->id;

        if (!empty($validated['is_default'])) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address = Address::create($validated);

        return response()->json(['message' => 'Alamat berhasil disimpan', 'address' => $address], 201);
    }

    public function update(Request $request, Address $address): JsonResponse
    {
        if ($address->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'label' => 'nullable|string|max:50',
            'alamat' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'penerima' => 'nullable|string|max:100',
            'no_telp_penerima' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);

        if (!empty($validated['is_default'])) {
            $request->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json(['message' => 'Alamat berhasil diperbarui', 'address' => $address]);
    }

    public function destroy(Request $request, Address $address): JsonResponse
    {
        if ($address->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $address->delete();

        return response()->json(['message' => 'Alamat berhasil dihapus']);
    }
}
