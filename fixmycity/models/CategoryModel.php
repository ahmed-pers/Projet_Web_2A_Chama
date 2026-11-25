<?php
// Model (table problem_category): private attributes + getters/setters + fromArray(). No SQL here.

class CategoryModel
{
    private int $id = 0;
    private string $name = '';
    private int $departmentId = 0;
    private string $departmentName = '';   // joined from department (display only)
    private string $departmentEmail = '';  // joined from department (display only)
    private int $reportCount = 0;          // computed by the controller for the admin list

    public function __construct()
    {
    }

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getDepartmentId(): int { return $this->departmentId; }
    public function getDepartmentName(): string { return $this->departmentName; }
    public function getDepartmentEmail(): string { return $this->departmentEmail; }
    public function getReportCount(): int { return $this->reportCount; }

    public function setId(int $id): void { $this->id = $id; }
    public function setName(string $name): void { $this->name = $name; }
    public function setDepartmentId(int $departmentId): void { $this->departmentId = $departmentId; }
    public function setDepartmentName(string $departmentName): void { $this->departmentName = $departmentName; }
    public function setDepartmentEmail(string $departmentEmail): void { $this->departmentEmail = $departmentEmail; }
    public function setReportCount(int $reportCount): void { $this->reportCount = $reportCount; }

    public static function fromArray(array $row): self
    {
        $c = new self();
        $c->setId((int) ($row['id'] ?? 0));
        $c->setName((string) ($row['name'] ?? ''));
        $c->setDepartmentId((int) ($row['department_id'] ?? 0));
        $c->setDepartmentName((string) ($row['department_name'] ?? ''));
        $c->setDepartmentEmail((string) ($row['department_email'] ?? ''));
        $c->setReportCount((int) ($row['report_count'] ?? 0));
        return $c;
    }
}
