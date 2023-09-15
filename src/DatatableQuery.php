<?php
namespace Nuwebs\PrimevueDatatable;

class DatatableQuery
{
  private array $originalQuery;

  private int $currentPage;
  private int $rowsPerPage;
  private string|null $sortBy;
  private string $sortDirection;
  private array $filters;
  // The columns are used for the global search.
  private array $columns;
  private Filter|null $globalFilter;

  public function __construct(string|array $jsonQuery)
  {
    $this->originalQuery = is_array($jsonQuery) ? $jsonQuery : json_decode($jsonQuery, true);
    $tempObj = collect($this->originalQuery);

    $this->filters = [];
    $this->columns = $tempObj->get('columns', []);
    $this->currentPage = $tempObj->get('page', 1);
    $this->rowsPerPage = $tempObj->get('rows', 15);
    $this->sortBy = $tempObj->get('sortField');
    $this->sortDirection = $tempObj->get('sortOrder') == 1 ? 'asc' : 'desc';
    $this->globalFilter = null;

    $this->setFilters($tempObj->get('filters', []));
  }

  // Getter methods

  public function getOriginalQuery(): array
  {
    return $this->originalQuery;
  }

  public function getCurrentPage(): int
  {
    return $this->currentPage;
  }

  public function getRowsPerPage(): int
  {
    return $this->rowsPerPage;
  }

  public function getFilters(): array
  {
    return $this->filters;
  }

  public function getSortBy(): string | null
  {
    return $this->sortBy;
  }

  public function getSortDirection(): string
  {
    return $this->sortDirection;
  }

  public function getColumns(): array
  {
    return $this->columns;
  }

  public function getGlobalFilter(): Filter
  {
    return $this->globalFilter;
  }

  public function hasGlobalFilter(): bool
  {
    return !is_null($this->globalFilter);
  }

  private function setFilters(array $rawFilters): void
  {
    foreach ($rawFilters as $field => $rawFilter) {
      $filter = new Filter($field, $rawFilter['value'], $rawFilter['matchMode']);
      if ($field === 'global') {
        $this->globalFilter = $filter;
      } else {
        $this->filters[] = $filter;
      }
    }
  }
}