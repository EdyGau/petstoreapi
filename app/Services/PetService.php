<?php

namespace App\Services;

use App\Clients\PetStoreClient;
use Illuminate\Support\Facades\Http;

class PetService
{
    private PetStoreClient $petClient;

    public function __construct(PetStoreClient $petClient)
    {
        $this->petClient = $petClient;
    }

    public function fetchPaginatedPets(string $status, int $currentPage): array
    {
        $perPage = 10;
        $pets = $this->petClient->getFilteredPets($status) ?? [];

        $paginatedPets = collect($pets)->forPage($currentPage, $perPage);

        return [
            'pets' => $paginatedPets,
            'currentPage' => $currentPage,
            'totalPages' => ceil(count($pets) / $perPage),
        ];
    }

    public function createPet(array $data)
    {
        $payload = [
            "id" => $data['id'] ?? 0,
            "name" => $data['name'],
            "status" => $data['status'],
        ];

        return $this->petClient->createPet($payload);
    }

    public function getPetById(int $id)
    {
        return $this->petClient->getPetById($id);
    }

    public function updatePet(array $data)
    {
        return $this->petClient->updatePet($data);
    }

    public function deletePet(int $id)
    {
        return $this->petClient->deletePet($id);
    }
}
