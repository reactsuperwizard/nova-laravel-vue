<?php

namespace App\Nova\Filters;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use philperusse\Filters\ColumnFilter;

class NumericRangeFilter extends ColumnFilter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    // public $component = 'select-filter';

    public function __construct()
    {
        
    }

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply( Request $request, $query, $value )
  {
     $args = collect($value)->values()->filter(); //Remove any empty keys.
     if($args->isEmpty())
          return $query;
          
      return $query->where(...$args->all());
  }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return array_merge(parent::options($request), [
            'columns' => [
                'name'      => 'Name',
                'age'       => 'Age',
            ]
        ]);
    }
}
