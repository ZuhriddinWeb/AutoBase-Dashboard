<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Geozone;
use Illuminate\Http\Request;

class GeozoneController extends Controller
{
    // Barcha geozonalarni chiqarish
    public function index()
    {
        $geozones = Geozone::orderBy('name', 'asc')->get();
        return response()->json($geozones);
    }

    // Yangi geozona qo'shish (agar qo'lda kiritilmoqchi bo'lsa)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|integer',
        ]);

        // Wialon va Resource ID qo'lda yozilganda 0 qilib qo'yamiz
        $validated['wialon_id'] = $request->wialon_id ?? rand(10000, 99999);
        $validated['resource_id'] = $request->resource_id ?? 0;

        $geozone = Geozone::create($validated);
        return response()->json($geozone, 201);
    }

    // Geozonani ko'rsatish
    public function show($id)
    {
        return response()->json(Geozone::findOrFail($id));
    }

    // Geozonani yangilash
    public function update(Request $request, $id)
    {
        $geozone = Geozone::findOrFail($id);
        $geozone->update($request->all());
        return response()->json($geozone);
    }

    // Geozonani o'chirish
    public function destroy($id)
    {
        Geozone::findOrFail($id)->delete();
        return response()->json(['message' => 'O\'chirildi']);
    }
}