<?php

namespace App\Services;

use App\Clients\PetStoreClient;

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
        try {
            return $this->petClient->createPet($data);
        } catch (\Exception $e) {
            throw $e;
        }
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
