<?php

namespace App\Clients;

use Exception;
use Illuminate\Support\Facades\Http;

class PetStoreClient
{
    private const BASE_URL = 'https://petstore.swagger.io/v2/pet';

    public function getFilteredPets(string $status): array|null
    {
        $response = Http::get(self::BASE_URL . '/findByStatus', [
            'status' => $status,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Error fetching pets: ' . $response->body());
    }

    /**
     * Fetches a pet by ID.
     *
     * @param int $id
     * @return array|null
     * @throws Exception
     */
    public function getPetById(int $id): array|null
    {
        $response = Http::get(self::BASE_URL . "/{$id}");
        if ($response->successful()) {
            return $response->json();
        }
        throw new Exception('Error fetching pet: ' . $response->body());
    }

    /**
     * Creates a new pet.
     *
     * @param array $data
     * @return array|null
     * @throws Exception
     */
    public function createPet(array $data): array|null
    {
        $response = Http::post(self::BASE_URL, $data);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Error creating pet: ' . $response->body());
    }

    /**
     * Updates an existing pet.
     *
     * @param array $data
     * @return array|null
     * @throws Exception
     */
    public function updatePet(array $data): array|null
    {
        $response = Http::put(self::BASE_URL, $data);
        if ($response->successful()) {
            return $response->json();
        }
        throw new Exception('Error updating pet: ' . $response->body());
    }

    /**
     * Deletes a pet by ID.
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function deletePet(int $id): bool
    {
        $response = Http::delete(self::BASE_URL . "/{$id}");
        return $response->successful();
    }
}
