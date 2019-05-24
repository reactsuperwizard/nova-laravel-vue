<?php

namespace Hdh\LastFilter;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class LastFilter extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'last-filter';

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
        return $query->whereBetween('credit', $value);
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
