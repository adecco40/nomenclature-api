<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use App\Models\ChangeHistory;
use Illuminate\Support\Facades\Auth;

class LogModelChanges
{
    protected function entityType(Model $model)
    {
        return strtolower(class_basename($model));
    }

    public function created(Model $model)
    {
        $userId = $model->created_by ?? Auth::id();
        ChangeHistory::create([
            'user_id' => $userId,
            'entity_type' => $this->entityType($model),
            'entity_id' => $model->id,
            'action' => 'created',
            'changes' => $model->toArray(),
        ]);
    }

    public function updated(Model $model)
    {
        $userId = $model->updated_by ?? Auth::id();
        $changes = $model->getChanges();
        $original = array_intersect_key($model->getOriginal(), $changes);

        ChangeHistory::create([
            'user_id' => $userId,
            'entity_type' => $this->entityType($model),
            'entity_id' => $model->id,
            'action' => 'updated',
            'changes' => ['old' => $original, 'new' => $changes],
        ]);
    }

    public function deleted(Model $model)
    {
        $userId = Auth::id();
        ChangeHistory::create([
            'user_id' => $userId,
            'entity_type' => $this->entityType($model),
            'entity_id' => $model->id,
            'action' => 'deleted',
            'changes' => $model->toArray(),
        ]);
    }
}
