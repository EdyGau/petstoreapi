<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Repositories\Implementations\PetRepository;
use Tests\TestCase;

class PetTest extends TestCase
{
    public function testUpdatePet(): void
    {
        $response = $this->put('/pets/1', ['name' => 'Diego', 'status' => 'sold']);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}
