<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UniversalController extends Controller
{
    /**
     * Resurs nomidan Modelning to'liq namespace'ini aniqlash.
     */
    protected function getModel($resource)
    {
        // Muhim: Boshiga \ qo'shish PHP-ga root namespace'dan qidirishni aytadi
        $modelClass = "\\App\\Models\\" . Str::studly(Str::singular($resource));

        if (!class_exists($modelClass)) {
            // Agar model topilmasa, xatolik qaytaramiz
            abort(404, "Model $modelClass topilmadi");
        }

        return $modelClass;
    }

    public function index($resource)
    {
        $model = $this->getModel($resource);
        
        $query = $model::latest();

        // Agar Transport Guruhlari so'ralayotgan bo'lsa, ichidagi mashinalarni ham qo'shib olamiz
        if ($resource === 'transport_groups') {
            $query->with('machines');
        }

        return $query->get();
    }

    public function store(Request $request, $resource)
    {
        $model = $this->getModel($resource);
        $data = $request->all();

        // Foydalanuvchi uchun maxsus mantiq
        if ($resource === 'users') {
            $data['password'] = Hash::make($request->password ?? '123456');
        }

        // Dinamik model orqali yaratish
        $item = $model::create($data);

        // 2. Agar "machines" degan massiv kelsa, ularni pivot jadvalga ulaymiz
        if ($request->has('machines') && is_array($request->machines)) {
            // 'machines()' funksiyasi TransportGroup modelida bo'lishi shart!
            $item->machines()->sync($request->machines);
        }

        return $item;
    }

public function update(Request $request, $resource, $id)
    {
        $model = $this->getModel($resource);
        $item = $model::findOrFail($id);

        $data = $request->all();

        if ($resource === 'users') {
            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $data['password'] = Hash::make($data['password']);
            }
        }

        // 1. Asosiy ma'lumotni yangilaymiz
        $item->update($data);

        // 2. Agar "machines" o'zgargan bo'lsa, uni ham yangilaymiz
        if ($request->has('machines') && is_array($request->machines)) {
            $item->machines()->sync($request->machines);
        }

        return $item;
    }

public function destroy($resource, $id)
    {
        $model = $this->getModel($resource);
        $item = $model::findOrFail($id);
        $item->delete();
        return response()->json(['success' => true]);
    }
}