<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Jobs\ProcessProductImportBatch;

class ProductsImport implements ToCollection, WithHeadingRow
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function collection(Collection $rows)
    {
        // $rows — коллекция ассоциативных массивов (HeadingRow)
        $rows->chunk(100)->each(function ($chunk) {
            ProcessProductImportBatch::dispatch($chunk->toArray(), $this->userId);
        });
    }
}