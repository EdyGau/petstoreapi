<?php

namespace App\Http\Controllers;

use App\Services\PetService;
use Illuminate\Http\Request;

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
     * Display a paginated list of pets.
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
     * Display the form to create a new pet.
     */
    public function create()
    {
        return view('pets.create');
    }

    /**
     * Create a new pet.
     */
    public function store(Request $request)
    {
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:available,pending,sold',
        ]);

        try {
            $this->petService->createPet($data);
            return redirect()->route('pets.index')->with('success', 'Pet added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show edit form for a specific pet.
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
     * Update a specific pet.
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

    public function destroy(int $id)
    {
        $this->petService->deletePet($id);
        return redirect()->route('pets.index')->with('success', 'Pet deleted successfully!');
    }

    /**
     * About this application.
     */
    public function about()
    {
        return view('layouts.about');
    }
}
