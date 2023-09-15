<?php

namespace Nuwebs\PrimevueDatatable;

use Illuminate\Database\Eloquent\Builder;

class Filter
{
    private string $likeOperator;
    private string $field;
    private string $value;
    private FilterMatchMode $matchMode;

    public function __construct(string $field, string $value = null, FilterMatchMode $matchMode = FilterMatchMode::CONTAINS)
    {
        $this->field = $field;
        $this->value = $value;
        $this->matchMode = $matchMode;
        $this->likeOperator = \DB::connection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'pgsql' ? 'ILIKE' : 'LIKE';
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getMatchMode(): FilterMatchMode
    {
        return $this->matchMode;
    }

    public function applyFilter(Builder &$q, ?bool $or = false): void
    {
        $relations = explode('.', $this->field);
        $rLen = count($relations);
        if ($rLen > 1) {
            // If the filter has a relation path, take the relation and pass it to
            // the orWhereHas or whereHas.
            $relPath = implode('.', array_slice($relations, 0, $rLen - 1));
            $relCol = end($relations);
            $func = $or ? 'orWhereHas' : 'whereHas';
            $q->$func($relPath, function ($newQ) use ($relCol, $or) {
                return $this->applyWhere($newQ, $relCol, $or);
            });
            return;
        }
        $this->applyWhere($q, $relations[0], $or);
    }
    private function applyWhere(Builder $q, string $field, bool $or = false): Builder
    {
        $func = $or ? 'orWhere' : 'where';
        $dateFunc = $or ? 'orWhereDate' : 'whereDate';

        switch ($this->matchMode) {
            case FilterMatchMode::STARTS_WITH:
                return $q->$func($field, $this->likeOperator, $this->value . '%');

            case FilterMatchMode::NOT_CONTAINS:
                return $q->$func($field, 'NOT' . $this->likeOperator, "%$this->value%");
            
            case FilterMatchMode::ENDS_WITH:
                return $q->$func($field, $this->likeOperator, '%' . $this->value);
            
            case FilterMatchMode::EQUALS:
                return $q->$func($field, '=', $this->value);

            case FilterMatchMode::NOT_EQUALS:
                return $q->$func($field, '!=', $this->value);
            
            case FilterMatchMode::IN:
                //TODO: Implement
                return $q;

            case FilterMatchMode::LESS_THAN:
                return $q->$func($field, '<', $this->value);

            case FilterMatchMode::LESS_THAN_OR_EQUAL_TO:
                return $q->$func($field, '<=', $this->value);

            case FilterMatchMode::GREATER_THAN:
                return $q->$func($field, '>', $this->value);

            case FilterMatchMode::GREATER_THAN_OR_EQUAL_TO:
                return $q->$func($field, '>=', $this->value);

            case FilterMatchMode::BETWEEN:
                //TODO: implement
                return $q;

            case FilterMatchMode::DATE_IS:
                return $q->$dateFunc($field, '=', $this->value);

            case FilterMatchMode::DATE_IS_NOT:
                return $q->$dateFunc($field, '!=', $this->value);

            case FilterMatchMode::DATE_BEFORE:
                return $q->$dateFunc($field, '<=', $this->value);

            case FilterMatchMode::DATE_AFTER:
                return $q->$dateFunc($field, '>', $this->value);
            
            case FilterMatchMode::CONTAINS:
            default:
                return $q->$func($field, $this->likeOperator, "%$this->value%");
        }
    }
}