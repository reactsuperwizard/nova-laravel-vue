<?php

/**
 * @author Chenglu
 * @since April 2019
*/

namespace Acme\AgeRange;

use Illuminate\Http\Request;
use Illuminate\Container\Container;
use Laravel\Nova\Filters\Filter;
use Log;
class AgeRange extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'age-range';

    /**
     * The filter's field name.
     *
     * @var string
     */

    public $field = '';

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
    public function apply(Request $request, $query, $value)
    {
        // var_dump($value);
        if ($this->field == '') 
            return $query;
        else
            return $query->whereBetween($this->field, $value);
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

    public function min($min_value)
    {
        $this->withMeta(['minRange' => $min_value]);
        return $this;
    }

    public function max($max_value)
    {
        $this->withMeta(['maxRange' => $max_value]);
        return $this;
    }

    public function step($interval_val)
    {
        $this->withMeta(['intervalVal' => $interval_val]);
        return $this;
    }

    public function setFieldName($field_name)
    {
        $this->field = $field_name;
        return $this;
    }
}
