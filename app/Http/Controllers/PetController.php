<?php

namespace App\Http\Controllers;

use App\Services\PetService;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class PetController extends Controller
{
    private PetService $petService;

    /**
     * Inject PetService into the controller.
     *
     * @param PetService $petService
     */
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
        $status = $request->get('status', 'available');
        $currentPage = (int) $request->get('page', 1);

        $result = $this->petService->fetchPaginatedPets($status, $currentPage);

        return view('pets.index', [
            'pets' => $result['pets'],
            'currentPage' => $result['currentPage'],
            'totalPages' => $result['totalPages'],
            'statusFilter' => $status,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/pets/create",
     *     summary="Display the form to create a new pet",
     *     @OA\Response(
     *         response=200,
     *         description="Form to create a new pet"
     *     )
     * )
     */
    public function create()
    {
        return view('pets.create');
    }

    /**
     * @OA\Post(
     *     path="/pets",
     *     summary="Create a new pet",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="status", type="string", enum={"available","pending","sold"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pet created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:available,pending,sold',
        ]);

        $response = $this->petService->createPet($data);

        if (isset($response['code'])) {
            return back()->withErrors(['error' => 'Failed to add pet: ' . $response['message']]);
        }

        return redirect()->route('pets.index')->with('success', 'Pet added successfully!');
    }

    /**
     * @OA\Get(
     *     path="/pets/{id}/edit",
     *     summary="Display the form to edit a pet",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the pet to edit",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form to edit the pet"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pet not found"
     *     )
     * )
     */
    public function edit(int $id)
    {
        $pet = $this->petService->getPetById($id);

        if (!$pet) {
            return redirect()->route('pets.index')->with('error', 'Pet not found');
        }

        return view('pets.edit', compact('pet'));
    }

    /**
     * @OA\Put(
     *     path="/pets/{id}",
     *     summary="Update a pet",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the pet to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="status", type="string", enum={"available","pending","sold"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pet updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pet not found"
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'status' => 'required|string|in:available,pending,sold',
        ]);
        $data['id'] = $id;

        $this->petService->updatePet($data);
        return redirect()->route('pets.index')->with('success', 'Pet updated successfully!');
    }

    /**
     * @OA\Delete(
     *     path="/pets/{id}",
     *     summary="Delete a pet",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the pet to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pet deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pet not found"
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $this->petService->deletePet($id);
        return redirect()->route('pets.index')->with('success', 'Pet deleted successfully!');
    }

    /**
     * @OA\Get(
     *     path="/about",
     *     summary="About this application",
     *     @OA\Response(
     *         response=200,
     *         description="About page"
     *     )
     * )
     */
    public function about()
    {
        return view('layouts.about');
    }
}
