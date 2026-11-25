<?php
declare(strict_types=1);

class ItemModel
{
    private int $id = 0;
    private int $categoryId = 0;
    private int $ownerId = 0;
    private string $name = '';
    private ?string $description = null;
    private string $type = 'lost';
    private string $status = 'pending';
    private string $dateFound = '';
    private ?string $imageUrl = null;
    private string $createdAt = '';

    // Joined data (display only).
    private string $categoryName = '';
    private string $ownerName = '';

    public function getId(): int { return $this->id; }
    public function getCategoryId(): int { return $this->categoryId; }
    public function getOwnerId(): int { return $this->ownerId; }
    public function getName(): string { return $this->name; }
    public function getDescription(): ?string { return $this->description; }
    public function getType(): string { return $this->type; }
    public function getStatus(): string { return $this->status; }
    public function getDateFound(): string { return $this->dateFound; }
    public function getImageUrl(): ?string { return $this->imageUrl; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function getCategoryName(): string { return $this->categoryName; }
    public function getOwnerName(): string { return $this->ownerName; }

    public function setId(int $id): void { $this->id = $id; }
    public function setCategoryId(int $id): void { $this->categoryId = $id; }
    public function setOwnerId(int $id): void { $this->ownerId = $id; }
    public function setName(string $name): void { $this->name = $name; }
    public function setDescription(?string $description): void { $this->description = $description; }
    public function setType(string $type): void { $this->type = $type; }
    public function setStatus(string $status): void { $this->status = $status; }
    public function setDateFound(string $date): void { $this->dateFound = $date; }
    public function setImageUrl(?string $url): void { $this->imageUrl = $url; }
    public function setCreatedAt(string $date): void { $this->createdAt = $date; }
    public function setCategoryName(string $name): void { $this->categoryName = $name; }
    public function setOwnerName(string $name): void { $this->ownerName = $name; }

    public static function fromArray(array $row): self
    {
        $item = new self();
        $item->setId((int) ($row['id_item'] ?? 0));
        $item->setCategoryId((int) ($row['id_category'] ?? 0));
        $item->setOwnerId((int) ($row['owner_id'] ?? 0));
        $item->setName((string) ($row['item_name'] ?? ''));
        $item->setDescription(isset($row['description']) && $row['description'] !== '' ? (string) $row['description'] : null);
        $item->setType((string) ($row['type'] ?? 'lost'));
        $item->setStatus((string) ($row['status'] ?? 'pending'));
        $item->setDateFound((string) ($row['date_found'] ?? ''));
        $item->setImageUrl(isset($row['image_url']) && $row['image_url'] !== '' ? (string) $row['image_url'] : null);
        $item->setCreatedAt((string) ($row['created_at'] ?? ''));
        $item->setCategoryName((string) ($row['category_name'] ?? ''));
        $item->setOwnerName((string) ($row['owner_name'] ?? ''));
        return $item;
    }
}
