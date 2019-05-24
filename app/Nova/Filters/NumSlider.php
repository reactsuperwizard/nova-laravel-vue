<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

use RP\Filters\NovaSlider;
use Acme\AgeRange\AgeRange;

use Log;

class NumSlider extends NovaSlider
{
    /**
     * The filter's component.
     *
     * @var string
     */
    // public $component = 'select-filter';
    /**
     * The filter's name
     * @var string
    */
    public $name = 'CreditSlider';

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
        Log::info("NumSlider");
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
        // custom configuration here
        Log::info("slider acme options");
        return [
            'width'         => "100%", // default "100%"
            'height'        => "8", // default "8"
            'minimum'       => 0, // default 0
            'maximum'       => 100 // default 100
        ];
    }
}
