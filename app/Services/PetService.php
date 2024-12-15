<?php

namespace App\Services;

use App\Clients\PetStoreClient;
use App\DTO\PetDTO;
use App\Events\PetCreated;
use App\Events\PetDeleted;
use App\Events\PetUpdated;
use App\Exceptions\PetApiException;
use Illuminate\Support\Facades\Log;

class PetService
{
    private PetStoreClient $client;

    public function __construct()
    {
        $this->client = new PetStoreClient();
    }

    /**
     * Fetch pets based on status with pagination.
     *
     * @throws \Exception
     */
    public function fetchPets(string $status, int $page = 1, int $perPage = 10): array
    {
        try {
            $responseData = $this->client->getFilteredPets($status);

            $totalItems = count($responseData);
            $paginatedItems = array_slice($responseData, ($page - 1) * $perPage, $perPage);

            return [
                'pets' => $paginatedItems,
                'currentPage' => $page,
                'totalPages' => ceil($totalItems / $perPage),
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching pets', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Create a new pet.
     * @throws PetApiException
     */
    public function createPet(array $data): PetDTO
    {
        try {
            $responseData = $this->client->createPet($data);

            if (empty($responseData) || !isset($responseData['id'])) {
                throw new PetApiException('Invalid response received from API.');
            }

            Log::info('Response received from API:', ['response' => $responseData]);

            $petDTO = PetDTO::fromArray($responseData);

            event(new PetCreated($petDTO));

            return $petDTO;
        } catch (\Exception $e) {
            Log::error('Service encountered an exception during createPet', ['error' => $e->getMessage()]);
            throw new PetApiException("Failed to create pet.");
        }
    }



    /**
     * Update an existing pet.
     *
     * @throws PetApiException
     */
    public function updatePet(array $data): PetDTO
    {
        try {
            $responseData = $this->client->updatePet($data);

            event(new PetUpdated(PetDTO::fromArray($responseData)));

            return PetDTO::fromArray($responseData);
        } catch (\Exception $e) {
            Log::error('Error updating pet', ['error' => $e->getMessage()]);
            throw new PetApiException("Failed to update pet.");
        }
    }

    /**
     * Fetch a specific pet by ID
     *
     * @throws PetApiException
     */
    public function getPetById(int $id): PetDTO
    {
        try {
            $responseData = $this->client->getPetById($id);
            return PetDTO::fromArray($responseData);
        } catch (\Exception $e) {
            Log::error('Error fetching pet by ID', ['error' => $e->getMessage()]);
            throw new PetApiException("Failed to fetch pet by ID.");
        }
    }

    /**
     * Delete a specific pet by ID
     * @throws PetApiException
     */
    public function deletePet(int $id): bool
    {
        try {
            $result = $this->client->deletePet($id);
            return $result;
        } catch (\Exception $e) {
            Log::error('Error deleting pet', ['error' => $e->getMessage()]);
            throw new PetApiException("Failed to delete pet.");
        }
    }
}
