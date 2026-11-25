<?php
// Model (table report): private attributes + getters/setters + fromArray(). No SQL here.

class ReportModel
{
    private int $id = 0;
    private int $userId = 0;
    private int $categoryId = 0;
    private string $photo = '';
    private ?string $address = null;
    private ?float $latitude = null;
    private ?float $longitude = null;
    private ?string $description = null;
    private string $status = 'pending';
    private string $createdAt = '';

    // joined data (display only)
    private string $categoryName = '';
    private string $departmentName = '';
    private string $departmentEmail = '';
    private string $username = '';

    public function __construct()
    {
    }

    public function getId(): int { return $this->id; }
    public function getUserId(): int { return $this->userId; }
    public function getCategoryId(): int { return $this->categoryId; }
    public function getPhoto(): string { return $this->photo; }
    public function getAddress(): ?string { return $this->address; }
    public function getLatitude(): ?float { return $this->latitude; }
    public function getLongitude(): ?float { return $this->longitude; }
    public function getDescription(): ?string { return $this->description; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function getCategoryName(): string { return $this->categoryName; }
    public function getDepartmentName(): string { return $this->departmentName; }
    public function getDepartmentEmail(): string { return $this->departmentEmail; }
    public function getUsername(): string { return $this->username; }

    public function setId(int $id): void { $this->id = $id; }
    public function setUserId(int $userId): void { $this->userId = $userId; }
    public function setCategoryId(int $categoryId): void { $this->categoryId = $categoryId; }
    public function setPhoto(string $photo): void { $this->photo = $photo; }
    public function setAddress(?string $address): void { $this->address = $address; }
    public function setLatitude(?float $latitude): void { $this->latitude = $latitude; }
    public function setLongitude(?float $longitude): void { $this->longitude = $longitude; }
    public function setDescription(?string $description): void { $this->description = $description; }
    public function setStatus(string $status): void { $this->status = $status; }
    public function setCreatedAt(string $createdAt): void { $this->createdAt = $createdAt; }
    public function setCategoryName(string $categoryName): void { $this->categoryName = $categoryName; }
    public function setDepartmentName(string $departmentName): void { $this->departmentName = $departmentName; }
    public function setDepartmentEmail(string $departmentEmail): void { $this->departmentEmail = $departmentEmail; }
    public function setUsername(string $username): void { $this->username = $username; }

    public static function fromArray(array $row): self
    {
        $r = new self();
        $r->setId((int) ($row['id'] ?? 0));
        $r->setUserId((int) ($row['user_id'] ?? 0));
        $r->setCategoryId((int) ($row['category_id'] ?? 0));
        $r->setPhoto((string) ($row['photo'] ?? ''));
        $r->setAddress(isset($row['address']) ? (string) $row['address'] : null);
        $r->setLatitude(isset($row['latitude']) ? (float) $row['latitude'] : null);
        $r->setLongitude(isset($row['longitude']) ? (float) $row['longitude'] : null);
        $r->setDescription(isset($row['description']) ? (string) $row['description'] : null);
        $r->setStatus((string) ($row['status'] ?? 'pending'));
        $r->setCreatedAt((string) ($row['created_at'] ?? ''));
        $r->setCategoryName((string) ($row['category_name'] ?? ''));
        $r->setDepartmentName((string) ($row['department_name'] ?? ''));
        $r->setDepartmentEmail((string) ($row['department_email'] ?? ''));
        $r->setUsername((string) ($row['username'] ?? ''));
        return $r;
    }
}
