<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends BaseController
{
    public function index(Request $request)
    {
        $suppliers = Supplier::all();
        return $this->successResponse('Записи успешно получены', $suppliers);
    }

    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);
        return $this->successResponse('Запись успешно получена', $supplier);
    }

    public function store(SupplierRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $supplier = Supplier::create($data);
        return $this->successResponse('Поставщик создан', $supplier, 201);
    }

    public function update(SupplierRequest $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $data = $request->validated();
        $data['updated_by'] = auth()->id();
        $supplier->update($data);
        return $this->successResponse('Поставщик обновлён', $supplier);
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        return $this->successResponse('Поставщик удалён');
    }
}
