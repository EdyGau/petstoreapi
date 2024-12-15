<?php

namespace App\Repositories\Interfaces;

use App\Models\Pet;

interface PetRepositoryInterface
{
    public function findById(int $id): ?Pet;
    public function create(array $data): Pet;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
