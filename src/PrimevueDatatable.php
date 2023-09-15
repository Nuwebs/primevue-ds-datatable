<?php

namespace Nuwebs\PrimevueDatatable;

use Illuminate\Database\Eloquent\Builder;

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
                // The first column must be where instead of orWhere.
                $firstColumn = $columns[0];
                $otherColumns = array_slice($columns, 1);
                $firstFilter = new Filter($firstColumn, $globalFilter->getValue(), $globalFilter->getMatchMode());
                $firstFilter->applyFilter($q);
                foreach ($otherColumns as $column) {
                    $colFilter = new Filter($column, $globalFilter->getValue(), $globalFilter->getMatchMode());
                    $colFilter->applyFilter($q, true);
                }
            }
        })->where(function (Builder $q) use ($params) {
            // Local filters
            foreach ($params->getFilters() as $filter) {
                if (!empty($filter->getValue())) {
                    $filter->applyFilter($q);
                }
            }
        });
        
        //This checks if the query has relations. Needed to avoid the N+1 problem.
        $with = [];
        foreach ($columns as $column){
            $exploded = explode('.', $column);
            $len = count($exploded);
            if ($len > 1) {
                // Add the relation. The slice takes all elements from start to end, excluding the last colum
                // ex: posts.user.name => posts.user
                $with[] = implode('.', array_slice($exploded, 0, $len - 1));
            }
        }
        $query->with($with);

        $this->applySort($query);
        return $query->paginate($this->dtQueryParams->getRowsPerPage(), page: $this->dtQueryParams->getCurrentPage());
    }

    private function applySort(Builder &$q): void
    {
        if (empty($this->dtQueryParams->getSortBy()))
            return;
        $q->orderBy($this->dtQueryParams->getSortBy(), $this->dtQueryParams->getSortDirection());
    }
}