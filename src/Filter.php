<?php

namespace Nuwebs\PrimevueDatatable;

use Illuminate\Database\Eloquent\Builder;

class Filter
{
    const STARTS_WITH = 'startsWith';
    const CONTAINS = 'contains';
    const NOT_CONTAINS = 'notContains';
    const ENDS_WITH = 'endsWith';
    const EQUALS = 'equals';
    const NOT_EQUALS = 'notEquals';
    const IN = 'in';
    const LESS_THAN = 'lt';
    const LESS_THAN_OR_EQUAL_TO = 'lte';
    const GREATER_THAN = 'gt';
    const GREATER_THAN_OR_EQUAL_TO = 'gte';
    const BETWEEN = 'between';
    const DATE_IS = 'dateIs';
    const DATE_IS_NOT = 'dateIsNot';
    const DATE_BEFORE = 'dateBefore';
    const DATE_AFTER = 'dateAfter';

    private string $likeOperator;
    private string $field;
    private string $value;
    private string $matchMode;

    public function __construct(string $field, string $value = null, string $matchMode = self::CONTAINS)
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
    public function getMatchMode(): string
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
        // NEEDS TO BE HEAVILY REFACTORED
        $jsonField = $this->isJsonFieldPath($field);

        switch ($this->matchMode) {
            case self::STARTS_WITH:
                if ($or) {
                    if (!$jsonField) {
                        return $q->orWhere($field, $this->likeOperator, $this->value . "%");
                    } else {
                        return $q->orWhereRaw('LOWER(' . $jsonField[0] . '->>"$.' . $jsonField[1] . '") ' . $this->likeOperator . ' ?', mb_strtolower($this->value . "%"));
                    }
                } else {
                    if (!$jsonField) {
                        return $q->where($field, $this->likeOperator, $this->value . "%");
                    } else {
                        return $q->whereRaw('LOWER(' . $jsonField[0] . '->>"$.' . $jsonField[1] . '") ' . $this->likeOperator . ' ?', mb_strtolower($this->value . "%"));
                    }
                }
            case self::NOT_CONTAINS:
                if ($or) {
                    if (!$jsonField) {
                        return $q->orWhere($field, "NOT" . $this->likeOperator, "%" . $this->value . "%");
                    } else {
                        return $q->orWhereRaw('LOWER(' . $jsonField[0] . '->>"$.' . $jsonField[1] . '") NOT ' . $this->likeOperator . ' ?', mb_strtolower("%" . $this->value . "%"));
                    }
                } else {
                    if (!$jsonField) {
                        return $q->where($field, "NOT" . $this->likeOperator, "%" . $this->value . "%");
                    } else {
                        return $q->whereRaw('LOWER(' . $jsonField[0] . '->>"$.' . $jsonField[1] . '") NOT ' . $this->likeOperator . ' ?', mb_strtolower("%" . $this->value . "%"));
                    }
                }
            case self::ENDS_WITH:
                if ($or) {
                    if (!$jsonField) {
                        return $q->orWhere($field, $this->likeOperator, "%" . $this->value);
                    } else {
                        return $q->orWhereRaw('LOWER(' . $jsonField[0] . '->>"$.' . $jsonField[1] . '") ' . $this->likeOperator . ' ?', mb_strtolower("%" . $this->value . ""));
                    }
                } else {
                    if (!$jsonField) {
                        return $q->where($field, $this->likeOperator, "%" . $this->value);
                    } else {
                        return $q->whereRaw('LOWER(' . $jsonField[0] . '->>"$.' . $jsonField[1] . '") ' . $this->likeOperator . ' ?', mb_strtolower("%" . $this->value . ""));
                    }
                }
            case self::EQUALS:
                if ($or) {
                    if (!$jsonField) {
                        return $q->orWhere($field, "=", $this->value);
                    } else {
                        return $q->orWhereRaw('LOWER(' . $jsonField[0] . '->>"$.' . $jsonField[1] . '") = ?', mb_strtolower($this->value));
                    }
                } else {
                    if (!$jsonField) {
                        return $q->where($field, "=", $this->value);
                    } else {
                        return $q->whereRaw('LOWER(' . $jsonField[0] . '->>"$.' . $jsonField[1] . '") = ?', mb_strtolower($this->value));
                    }
                }
            case self::NOT_EQUALS:
                if ($or) {
                    if (!$jsonField) {
                        return $q->orWhere($field, "!=", $this->value);
                    } else {
                        return $q->orWhereRaw('LOWER(' . $jsonField[0] . '->>"$.' . $jsonField[1] . '") != ?', mb_strtolower($this->value));
                    }
                } else {
                    if (!$jsonField) {
                        return $q->where($field, "!=", $this->value);
                    } else {
                        return $q->whereRaw('LOWER(' . $jsonField[0] . '->>"$.' . $jsonField[1] . '") != ?', mb_strtolower($this->value));
                    }
                }
            case self::IN:
                //TODO: Implement
                return $q;
            case self::LESS_THAN:
                if ($or) {
                    return $q->orWhere($field, "<", $this->value);
                } else {
                    return $q->where($field, "<", $this->value);
                }
            case self::LESS_THAN_OR_EQUAL_TO:
                if ($or) {
                    return $q->orWhere($field, "<=", $this->value);
                } else {
                    return $q->where($field, "<=", $this->value);
                }
            case self::GREATER_THAN:
                if ($or) {
                    return $q->orWhere($field, ">", $this->value);
                } else {
                    return $q->where($field, ">", $this->value);
                }
            case self::GREATER_THAN_OR_EQUAL_TO:
                if ($or) {
                    return $q->orWhere($field, ">=", $this->value);
                } else {
                    return $q->where($field, ">=", $this->value);
                }
            case self::BETWEEN:
                //TODO: implement
                return $q;

            case self::DATE_IS:
                if ($or) {
                    return $q->orWhereDate($field, "=", $this->value);
                } else {
                    return $q->whereDate($field, "=", $this->value);
                }

            case self::DATE_IS_NOT:
                if ($or) {
                    return $q->orWhereDate($field, "!=", $this->value);
                } else {
                    return $q->whereDate($field, "!=", $this->value);
                }

            case self::DATE_BEFORE:
                if ($or) {
                    return $q->orWhereDate($field, "<=", $this->value);
                } else {
                    return $q->whereDate($field, "<=", $this->value);
                }
            case self::DATE_AFTER:
                if ($or) {
                    return $q->orWhereDate($field, ">", $this->value);
                } else {
                    return $q->whereDate($field, ">", $this->value);
                }

            case self::CONTAINS:
            default:
                if ($or) {
                    if (!$jsonField) {
                        return $q->orWhere($field, $this->likeOperator, "%" . $this->value . "%");
                    } else {
                        return $q->orWhereRaw('LOWER(' . $jsonField[0] . '->>"$.' . $jsonField[1] . '") ' . $this->likeOperator . ' ?', mb_strtolower("%" . $this->value . "%"));
                    }
                } else {
                    if (!$jsonField) {
                        return $q->where($field, $this->likeOperator, "%" . $this->value . "%");
                    } else {
                        return $q->whereRaw('LOWER(' . $jsonField[0] . '->>"$.' . $jsonField[1] . '") ' . $this->likeOperator . ' ?', mb_strtolower("%" . $this->value . "%"));
                    }
                }
        }
    }

    /**
     * Check if a field string represents a JSON field path.
     *
     * @param string $field The field string to check.
     * @return array|false Returns an array of path segments if the field is a JSON field path,
     *                    or false otherwise.
     */
    private function isJsonFieldPath(string $field): false|array
    {
        if (str_contains($field, "->")) {
            return explode("->", $field);
        }
        return false;
    }
}