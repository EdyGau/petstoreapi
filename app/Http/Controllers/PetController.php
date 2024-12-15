<?php

namespace App\Http\Controllers;

use AllowDynamicProperties;
use App\Http\Requests\PetRequest;
use App\Services\PetService;
use App\Exceptions\PetStoreClientException;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;

#[AllowDynamicProperties]
class PetController extends Controller
{
    private PetService $petService;

    public function __construct(PetService $petService)
    {
        $this->petService = $petService;
    }

    /**
     * @OA\Get(
     *     path="/pets",
     *     summary="List pets with pagination",
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter pets by status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"available","pending","sold"})
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="pets", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="currentPage", type="integer"),
     *             @OA\Property(property="totalPages", type="integer")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $status = $request->get('status', 'available');
            $currentPage = (int) $request->get('page', 1);

            if (!in_array($status, ['available', 'pending', 'sold'])) {
                return back()->withErrors(['error' => 'Invalid status value']);
            }

            if ($currentPage < 1) {
                return back()->withErrors(['error' => 'Invalid page value']);
            }

            $result = $this->petService->fetchPets($status, $currentPage);

            Log::info('Pets index request successful.', ['status' => $status, 'currentPage' => $currentPage]);

            return view('pets.index', [
                'pets' => $result['pets'],
                'currentPage' => $result['currentPage'],
                'totalPages' => $result['totalPages'],
                'statusFilter' => $status,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch pets: ', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Unable to fetch pets']);
        }
    }

    public function create()
    {
        return view('pets.create');
    }

    public function store(PetRequest $request)
    {
        try {
            $data = $request->validated();

            $createdPet = $this->petService->createPet($data);

            Log::info('Pet creation successful.', [
                'data_sent' => $data,
                'response_received' => $createdPet,
            ]);

            return redirect()->route('pets.index')->with('success', 'Pet added successfully!');
        } catch (PetStoreClientException $e) {
            Log::error('Failed to add pet: ', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('Unexpected error adding pet: ', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Unexpected error occurred.']);
        }
    }

    public function edit(int $id)
    {
        try {
            Log::info('Fetching pet for edit', ['id' => $id]);
            $pet = $this->petService->getPetById($id);

            if (!$pet) {
                return redirect()->route('pets.index')->withErrors(['error' => 'Pet not found']);
            }

            return view('pets.edit', compact('pet'));
        } catch (PetStoreClientException $e) {
            Log::error('Error fetching pet: ', ['error' => $e->getMessage()]);
            return redirect()->route('pets.index')->withErrors(['error' => 'Unable to fetch pet details']);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $data = $request->all();
            $data['id'] = $id;

            $this->petService->updatePet($data);

            Log::info('Pet update successful.', ['data' => $data]);

            return redirect()->route('pets.index')->with('success', 'Pet updated successfully!');
        } catch (PetStoreClientException $e) {
            Log::error('Error updating pet: ', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->petService->deletePet($id);

            Log::info('Pet deletion successful.', ['id' => $id]);

            return redirect()->route('pets.index')->with('success', 'Pet deleted successfully!');
        } catch (PetStoreClientException $e) {
            Log::error('Error deleting pet: ', ['error' => $e->getMessage()]);
            return redirect()->route('pets.index')->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function about()
    {
        return view('layouts.about');
    }
}
