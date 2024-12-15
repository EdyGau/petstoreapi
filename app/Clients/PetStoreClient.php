<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;
use App\Exceptions\PetApiException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PetStoreClient
{
    private const BASE_URL = 'https://petstore.swagger.io/v2';

    /**
     * @param string $status
     * @return array
     * @throws PetApiException
     */
    public function getFilteredPets(string $status): array
    {
        try {
            $response = Http::get(self::BASE_URL . '/pet/findByStatus', ['status' => $status]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new PetApiException("Failed to fetch pets by status.");
        } catch (\Exception $e) {
            Log::error('Error during getFilteredPets', ['error' => $e->getMessage()]);
            throw new PetApiException($e->getMessage());
        }
    }

    /**
     * @param int $id
     * @return array
     * @throws PetApiException
     */
    public function getPetById(int $id): array
    {
        try {
            if (Cache::has("pets_{$id}")) {
                Log::info('Cache hit for pet', ['id' => $id]);
                return Cache::get("pets_{$id}");
            }

            $response = Http::get(self::BASE_URL . "/pet/{$id}");

            if ($response->successful()) {
                $responseData = $response->json();
                Cache::put("pets_{$id}", $responseData, 60);
                Log::info('Caching data from API response', ['data' => $responseData]);
                return $responseData;
            }

            throw new PetApiException("Failed to fetch pet by ID.");
        } catch (\Exception $e) {
            Log::error('Error during getPetById', ['error' => $e->getMessage()]);
            throw new PetApiException($e->getMessage());
        }
    }

    public function createPet(array $data): array
    {
        try {
            Log::info('Sending HTTP request to create pet', ['url' => self::BASE_URL . "/pet", 'data' => $data]);

            $response = Http::post(self::BASE_URL . "/pet", $data);

            Log::info('HTTP response', ['response' => $response->json(), 'status' => $response->status()]);

            if ($response->successful()) {
                $responseData = $response->json();

                Log::info('Received response data', ['responseData' => $responseData]);

                if (isset($responseData['id'])) {
                    $uniqueKey = "pet_{$responseData['id']}";
                    Log::info('Attempting to cache data', ['key' => $uniqueKey, 'data' => $responseData]);

                    Cache::put($uniqueKey, $responseData, 60);
                    Log::info('Successfully cached data with unique key');
                } else {
                    Log::error('Response data does not contain id');
                }

                return $responseData;
            }

            throw new PetApiException("Failed to create a new pet.");
        } catch (\Exception $e) {
            Log::error('Error during createPet', ['error' => $e->getMessage()]);
            throw new PetApiException($e->getMessage());
        }
    }

    /**
     * @param array $data
     * @return array
     * @throws PetApiException
     */
    public function updatePet(array $data): array
    {
        try {
            $response = Http::put(self::BASE_URL . "/pet", $data);

            if ($response->successful()) {
                Cache::forget("pets_{$data['id']}");
                Log::info('Cache cleared after pet update.', ['id' => $data['id']]);
                return $response->json();
            }

            throw new PetApiException("Failed to update pet.");
        } catch (\Exception $e) {
            Log::error('Error during updatePet', ['error' => $e->getMessage()]);
            throw new PetApiException($e->getMessage());
        }
    }

    /**
     * @param int $id
     * @return bool
     * @throws PetApiException
     */
    public function deletePet(int $id): bool
    {
        try {
            $response = Http::delete(self::BASE_URL . "/pet/{$id}");

            if ($response->successful()) {
                Cache::forget("pets_{$id}");
                Log::info('Cache cleared after pet deletion.', ['id' => $id]);
                return true;
            }

            throw new PetApiException("Failed to delete pet.");
        } catch (\Exception $e) {
            Log::error('Error during deletePet', ['error' => $e->getMessage()]);
            throw new PetApiException($e->getMessage());
        }
    }
}
