<?php

namespace Nuwebs\PrimevueDatatable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use ReflectionClass;
use Throwable;

class PrimevueDatatable
{
    /**
     * @var Builder|\Illuminate\Database\Query\Builder
     */
    private \Illuminate\Database\Query\Builder|Builder $query;
    private DatatableQuery $dtQueryParams;

    public function __construct()
    {
        $this->dtQueryParams = new DatatableQuery(request()->get('dt_params', []));
    }

    private function setQuery(Builder $query): static
    {
        $this->query = $query;
        return $this;
    }
    public static function of(Builder $query): static
    {
        $instance = new self();
        return $instance->setQuery($query);
    }

    public function make(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $columns = $this->dtQueryParams->getColumns();
        $params = $this->dtQueryParams;
        $query = $this->query->where(function (Builder $q) use ($columns, $params) {
            // Global Search
            if ($params->hasGlobalFilter()) {
                $globalFilter = $params->getGlobalFilter();
                $firstColumn = $columns[0];
                $otherColumns = array_slice($columns, 1);
                $firstFilter = new Filter($firstColumn, $globalFilter->getValue(), $globalFilter->getMatchMode());
                $this->applyFilter($firstFilter, $q);
                foreach ($otherColumns as $column) {
                    $colFilter = new Filter($column, $globalFilter->getValue(), $globalFilter->getMatchMode());
                    $this->applyFilter($colFilter, $q, true);
                }
            }
        })->where(function (Builder $q) use ($params) {
            // Local filters
            foreach ($params->getFilters() as $filter) {
                if (!empty($filter->getValue())) {
                    $this->applyFilter($filter, $q);
                }
            }
        });
        
        $with = [];
        foreach ($columns as $column){
            $exploded = explode('.', $column);
            $len = count($exploded);
            if ($len > 1) {
                $with[] = implode('.', array_slice($exploded, 0, $len - 1));
            }
        }
        $query->with($with);

        $this->applySort($query);
        return $query->paginate($this->dtQueryParams->getRowsPerPage(), page: $this->dtQueryParams->getCurrentPage());
    }

    private function applyFilter(Filter $filter, Builder &$q, $or = false)
    {
        // Apply Search to a depth of 3
        $filter->buildWhere($q, $or);
    }

    private function applySort(Builder &$q): void
    {
        if (empty($this->dtQueryParams->getSortBy()))
            return;
        
        $key = explode(".", $this->dtQueryParams->getSortBy());

        if (sizeof($key) === 1) {
            $q->orderBy($this->dtQueryParams->getSortBy(), $this->dtQueryParams->getSortDirection());
        } elseif (sizeof($key) === 2) {
            $relationship = $this->getRelatedFromMethodName($key[0], get_class($q->getModel()));
            if ($relationship) {
                $parentTable = $relationship->getParent()->getTable();
                $relatedTable = $relationship->getRelated()->getTable();
                if ($relationship instanceof HasOne) {
                    $parentKey = explode(".", $relationship->getQualifiedParentKeyName())[1];
                    $relatedKey = $relationship->getForeignKeyName();
                } else {
                    $parentKey = $relationship->getForeignKeyName();
                    $relatedKey = $relationship->getOwnerKeyName();
                }

                $q->orderBy(
                    get_class($relationship->getRelated())::query()->select($key[1])->whereColumn("$parentTable.$parentKey", "$relatedTable.$relatedKey"),
                    $this->dtQueryParams->getSortDirection()
                );
            }
        }
    }

    private function getRelatedFromMethodName(string $method_name, string $class)
    {
        try {
            $method = (new ReflectionClass($class))->getMethod($method_name);
            return $method->invoke(new $class);
        } catch (Throwable $exception) {
            return null;
        }
    }
}