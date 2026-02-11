<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemLog;

class SystemLogController extends Controller
{
    public function index(Request $request)
    {
        $query = SystemLog::orderBy('created_at', 'desc');

        // Sana bo'yicha filtrlash
        if ($request->has('start_date') && $request->start_date != null) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != null) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Sahifalab yuborish (har sahifada 20 tadan)
        $logs = $query->paginate(20);

        // Ma'lumotlarni formatlash
        $logs->getCollection()->transform(function ($log) {
            return [
                'id' => $log->id,
                'user' => $log->user_name,
                'action' => $log->action,
                'time' => $log->created_at->format('H:i d.m.Y'), // To'liq sana
                'status' => $log->status
            ];
        });

        return response()->json($logs);
    }
}