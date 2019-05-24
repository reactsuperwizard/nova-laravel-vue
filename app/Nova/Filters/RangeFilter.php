<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Nova\Filters\Filter;
use Ampeco\Filters\DateRangeFilter;

class RangeFilter extends DateRangeFilter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    // public $component = 'select-filter';

    public function __construct()
    {

        // $this->placeholder("My Love")->dateFormat("m-d-Y");
    }

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        $from = Carbon::parse($value[0]);
        $to = Carbon::parse($value[1]);
        return $query;
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return [];
    }
}
