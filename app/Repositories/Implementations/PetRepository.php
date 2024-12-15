<?php
namespace App\Repositories\Implementations;

use App\Models\Pet;
use App\Repositories\Interfaces\PetRepositoryInterface;
use Illuminate\Support\Facades\Http;

class PetRepository implements PetRepositoryInterface
{
    private string $apiUrl = 'https://api.example.com/pets';

    public function getAll(string $status, int $page, int $perPage): array
    {
        $response = Http::get($this->apiUrl, [
            'status' => $status,
            'page' => $page,
            'per_page' => $perPage,
        ]);

        return $response->json();
    }

    public function findById(int $id): ?Pet
    {
        $response = Http::get("{$this->apiUrl}/{$id}");

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }

    public function create(array $data): Pet
    {
        $response = Http::post($this->apiUrl, $data);

        return $response->json();
    }

    public function update(int $id, array $data): bool
    {
        $response = Http::put("{$this->apiUrl}/{$id}", $data);

        return $response->successful();
    }

    public function delete(int $id): bool
    {
        $response = Http::delete("{$this->apiUrl}/{$id}");

        return $response->successful();
    }
}
