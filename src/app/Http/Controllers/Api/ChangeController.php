<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\ChangeHistory;
use Illuminate\Http\Request;

class ChangeController extends BaseController
{
    public function index(Request $request)
    {
        $query = ChangeHistory::query();
        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->get('entity_type'));
        }

        $perPage = (int) $request->get('per_page', 50);
        $data = $query->orderBy('created_at', 'desc')->paginate($perPage);
        return $this->successResponse('Записи успешно получены', $data);
    }
}
