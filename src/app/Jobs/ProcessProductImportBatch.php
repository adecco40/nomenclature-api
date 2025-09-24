<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;

class ProcessProductImportBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $rows;
    public $userId;

    public function __construct(array $rows, $userId)
    {
        $this->rows = $rows;
        $this->userId = $userId;
    }

    public function handle()
    {
        Log::info('Начало обработки пачки товаров', [
            'rows_count' => count($this->rows),
            'user_id' => $this->userId,
        ]);

        foreach ($this->rows as $index => $row) {
            try {
                // Нормализация ключей
                $row = array_change_key_case($row, CASE_LOWER);

                Log::debug('Обработка строки импорта', [
                    'row_index' => $index,
                    'row' => $row,
                ]);

                $rules = [
                    'id' => 'nullable|uuid|exists:products,id',
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'category_id' => 'nullable|uuid|exists:categories,id',
                    'supplier_id' => 'nullable|uuid|exists:suppliers,id',
                    'price' => 'required|numeric',
                    'file_url' => 'nullable|url',
                ];

                $validator = Validator::make($row, $rules);
                if ($validator->fails()) {
                    Log::warning('Валидация строки импорта не прошла', [
                        'row_index' => $index,
                        'errors' => $validator->errors()->all(),
                        'row' => $row,
                    ]);
                    continue;
                }

                DB::transaction(function () use ($row, $index) {
                    $data = [
                        'name' => $row['name'] ?? null,
                        'description' => $row['description'] ?? null,
                        'category_id' => $row['category_id'] ?? null,
                        'supplier_id' => $row['supplier_id'] ?? null,
                        'price' => isset($row['price']) ? (float)$row['price'] : 0,
                        'file_url' => $row['file_url'] ?? null,
                    ];

                    if (!empty($row['id'])) {
                        $product = Product::find($row['id']);
                        if ($product) {
                            $data['updated_by'] = $this->userId;
                            $product->update($data);
                            Log::info('Товар обновлён', [
                                'row_index' => $index,
                                'product_id' => $product->id,
                                'sku' => $product->sku ?? null,
                            ]);
                        } else {
                            $data['created_by'] = $this->userId;
                            $product = Product::create($data);
                            Log::info('Товар создан по id (не найден)', [
                                'row_index' => $index,
                                'product_id' => $product->id,
                            ]);
                        }
                    } else {
                        $data['created_by'] = $this->userId;
                        $product = Product::create($data);
                        Log::info('Новый товар создан', [
                            'row_index' => $index,
                            'product_id' => $product->id,
                            'sku' => $product->sku ?? null,
                        ]);
                    }
                });
            } catch (\Exception $e) {
                Log::error('Ошибка обработки строки импорта', [
                    'row_index' => $index,
                    'row' => $row,
                    'exception' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Обработка пачки товаров завершена');
    }
}
