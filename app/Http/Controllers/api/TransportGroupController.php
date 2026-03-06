<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\TransportGroup;
use Illuminate\Http\Request;

class TransportGroupController extends Controller
{
    public function index()
    {
        // Guruhlarni ichidagi mashinalar ro'yxati bilan birga olamiz
        return TransportGroup::with('machines')->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'wialon_id' => 'nullable|numeric', // Endi bu majburiy emas
            'machines' => 'nullable|array', // Tanlangan mashinalar ID lari
        ]);

        $group = TransportGroup::create([
            'name' => $data['name'],
            'wialon_id' => $data['wialon_id'] ?? 0, // Agar Wialondan kelmasa 0
        ]);

        // Mashinalarni biriktirish (Pivot jadvalga yozish)
        if (!empty($data['machines'])) {
            $group->machines()->sync($data['machines']);
        }

        return response()->json($group, 201);
    }

    public function update(Request $request, $id)
    {
        $group = TransportGroup::findOrFail($id);
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'machines' => 'nullable|array',
        ]);

        $group->update(['name' => $data['name']]);

        // Mashinalar ro'yxatini yangilash (eskisini o'chirib, yangisini yozadi)
        if (isset($data['machines'])) {
            $group->machines()->sync($data['machines']);
        }

        return response()->json($group);
    }

    public function destroy($id)
    {
        TransportGroup::findOrFail($id)->delete();
        return response()->json(['message' => 'O\'chirildi']);
    }
}