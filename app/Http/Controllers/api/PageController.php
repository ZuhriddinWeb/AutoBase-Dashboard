<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\SystemLog; // <--- Log modelini qo'shdik
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Barcha sahifalarni guruhlari bilan birga olish
     */
    public function index()
    {
        $pages = Page::with('groups')->orderBy('created_at', 'desc')->get();
        return response()->json($pages);
    }

    /**
     * Yangi sahifa yaratish
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'groups' => 'nullable|array',
        ]);

        $data = $request->except('groups');

        // Slug yasash
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        } else {
            $data['slug'] = Str::slug($data['slug']);
        }

        // Sahifani yaratish
        $page = Page::create($data);

        // Guruhlarni ulash
        if ($request->has('groups')) {
            $page->groups()->sync($request->input('groups'));
        }

        // --- LOG YOZISH ---
        SystemLog::record("Yangi sahifa yaratildi: " . $page->title);

        return response()->json([
            'message' => 'Sahifa muvaffaqiyatli yaratildi',
            'data' => $page
        ], 201);
    }

    /**
     * Sahifani ko'rish
     */
    public function show($id)
    {
        $page = Page::with('groups')->findOrFail($id);
        return response()->json($page);
    }

    /**
     * Sahifani yangilash
     */
    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);
        $data = $request->except('groups');

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        } else {
            $data['slug'] = Str::slug($data['slug']);
        }

        $page->update($data);

        if ($request->has('groups')) {
            $page->groups()->sync($request->input('groups', []));
        }

        // --- LOG YOZISH ---
        SystemLog::record("Sahifa tahrirlandi: " . $page->title);

        return response()->json([
            'message' => 'Sahifa muvaffaqiyatli yangilandi',
            'data' => $page
        ]);
    }

    /**
     * Sahifani o'chirish
     */
    public function destroy($id)
    {
        $page = Page::findOrFail($id);
        
        // Log uchun nomini saqlab olamiz
        $pageTitle = $page->title;

        $page->groups()->detach();
        $page->delete();

        // --- LOG YOZISH ---
        SystemLog::record("Sahifa o'chirildi: " . $pageTitle, 'Warning');

        return response()->json(['message' => 'Sahifa o\'chirildi']);
    }
}