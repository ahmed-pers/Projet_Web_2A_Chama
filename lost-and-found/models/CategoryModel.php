<?php
declare(strict_types=1);

class CategoryModel
{
    private int $id = 0;
    private string $name = '';
    private ?string $description = null;
    private int $itemCount = 0;

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getDescription(): ?string { return $this->description; }
    public function getItemCount(): int { return $this->itemCount; }

    public function setId(int $id): void { $this->id = $id; }
    public function setName(string $name): void { $this->name = $name; }
    public function setDescription(?string $description): void { $this->description = $description; }
    public function setItemCount(int $count): void { $this->itemCount = $count; }

    public static function fromArray(array $row): self
    {
        $category = new self();
        $category->setId((int) ($row['id_category'] ?? 0));
        $category->setName((string) ($row['name'] ?? ''));
        $category->setDescription(isset($row['description']) && $row['description'] !== '' ? (string) $row['description'] : null);
        $category->setItemCount((int) ($row['item_count'] ?? 0));
        return $category;
    }
}
