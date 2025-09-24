<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\ProductRequest;
use App\Imports\ProductsImport;
use Illuminate\Support\Str;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ProductController extends BaseController
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier']);

        if (!$request->boolean('with_inactive')) {
            $query->where('is_active', true);
        }

        $perPage = (int) $request->get('per_page', 15);
        $data = $query->paginate($perPage);

        return $this->successResponse('Записи успешно получены', $data);
    }

    public function show($id)
    {
        if (!Str::isUuid($id)) {
            return $this->errorResponse("Некорректный UUID: {$id}", ['error' => "incorrect field id"], 500);
        }

        $product = Product::with(['category', 'supplier'])->findOrFail($id);

        return $this->successResponse('Запись успешно получена', $product);
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('s3');

            $path = $request->file('file')->store('products', 's3');
            $data['file_url'] = $disk->url($path);
        }

        $data['created_by'] = auth()->id();

        $product = Product::create($data);

        return $this->successResponse('Товар создан', $product, 201);
    }

    public function update(ProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('file')) {
             /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('s3');

            $path = $request->file('file')->store('products', 's3');
            $data['file_url'] = $disk->url($path);
        }

        $data['updated_by'] = auth()->id();
        $product->update($data);

        return $this->successResponse('Товар обновлён', $product);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => false, 'updated_by' => auth()->id()]);

        return $this->successResponse('Товар удалён');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png|max:5120'
        ]);

        try {
             /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('s3');
            $path = $request->file('file')->store('products', 's3');
            $url = $disk->url($path);
            return $this->successResponse('Файл загружен', ['file_url' => $url]);
        } catch (\Exception $e) {
            Log::error('Upload error: ' . $e->getMessage());
            return $this->errorResponse('Ошибка при загрузке файла', ['error' => $e->getMessage()], 500);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $path = $request->file('file')->store('imports');
        // $fullPath = storage_path('app/' . $path);

        try {
            Excel::import(new ProductsImport(auth()->id()), $request->file('file'));
            return $this->successResponse('Импорт запущен. Каждая пачка обрабатывается в очереди.');
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return $this->errorResponse('Ошибка при запуске импорта', ['error' => $e->getMessage()], 500);
        }
    }
}
