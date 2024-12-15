<?php

namespace App\DTO;

class PetDTO
{
    public int $id;
    public string $name;
    public string $status;

    public ?string $category;
    public array $photoUrls;
    public array $tags;

    /**
     *
     * @param int $id
     * @param string $name
     * @param string $status
     * @param string|null $category
     * @param array $photoUrls
     * @param array $tags
     */
    public function __construct(
        int $id,
        string $name,
        string $status,
        ?string $category = null,
        array $photoUrls = [],
        array $tags = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->status = $status;
        $this->category = $category;
        $this->photoUrls = $photoUrls;
        $this->tags = $tags;
    }

    /**
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            name: $data['name'] ?? '',
            status: $data['status'] ?? 'unknown',
            category: $data['category']['name'] ?? null,
            photoUrls: $data['photoUrls'] ?? [],
            tags: array_map(
                fn($tag) => $tag['name'] ?? '',
                $data['tags'] ?? []
            )
        );
    }

    /**
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'category' => $this->category,
            'photoUrls' => $this->photoUrls,
            'tags' => $this->tags,
        ];
    }
}
