<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    public function index(Request $request)
    {
        $categories = Category::with('children')->get();
        return $this->successResponse('Записи успешно получены', $categories);
    }

    public function show($id)
    {
        $category = Category::with('children')->findOrFail($id);
        return $this->successResponse('Запись успешно получена', $category);
    }

    public function store(CategoryRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $category = Category::create($data);
        return $this->successResponse('Категория создана', $category, 201);
    }

    public function update(CategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $data = $request->validated();
        $data['updated_by'] = auth()->id();
        $category->update($data);
        return $this->successResponse('Категория обновлена', $category);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        // При необходимости добавить проверки (дочерние категории / связанные товары)
        $category->delete();
        return $this->successResponse('Категория удалена');
    }
}
