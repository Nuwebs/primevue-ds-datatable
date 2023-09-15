<?php

namespace Nuwebs\PrimevueDatatable\Filter;
enum FilterMatchMode: string
{
  // The case values are the exact same as PrimeVue values. DO NOT change this unless PrimeVue team
  // change their values. https://github.com/primefaces/primevue/blob/master/components/lib/api/FilterMatchMode.js
  case STARTS_WITH = 'startsWith';
  case CONTAINS = 'contains';
  case NOT_CONTAINS = 'notContains';
  case ENDS_WITH = 'endsWith';
  case EQUALS = 'equals';
  case NOT_EQUALS = 'notEquals';
  case IN = 'in';
  case LESS_THAN = 'lt';
  case LESS_THAN_OR_EQUAL_TO = 'lte';
  case GREATER_THAN = 'gt';
  case GREATER_THAN_OR_EQUAL_TO = 'gte';
  case BETWEEN = 'between';
  case DATE_IS = 'dateIs';
  case DATE_IS_NOT = 'dateIsNot';
  case DATE_BEFORE = 'dateBefore';
  case DATE_AFTER = 'dateAfter';

  public static function getAllValues(): array
  {
    return array_column(FilterMatchMode::cases(), 'value');
  }
}