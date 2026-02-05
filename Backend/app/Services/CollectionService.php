<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class CollectionService
{
    public function toggle(User $user, int $itemId, string $relationship, string $pivotField = 'donated_to_museum'): void
    {
        $user->{$relationship}()->toggle([$itemId => [$pivotField => true]]);
    }

    public function getUserItems(User $user, string $relationship): Collection
    {
        return $user->{$relationship}()->wherePivot('donated_to_museum', true)->get();
    }
}
