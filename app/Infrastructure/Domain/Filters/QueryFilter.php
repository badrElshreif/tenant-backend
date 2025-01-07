<?php

namespace App\Infrastructure\Domain\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class QueryFilter
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param Builder $builder
     */
    public function apply(Builder $builder)
    {
        $this->builder = $builder;
        foreach ($this->fields() as $field => $value) {
            $method = Str::camel($field);
            // dd($method);
            if (method_exists($this, $method)) {
                call_user_func_array([$this, $method], (array)$value);
            }
        }
    }

    /**
     * @return array
     */
    protected function fields(): array
    {
        // array_filter(
        return
            array_map(function ($item) {
                return !is_numeric($item) ? trim($item, " \n\r\t\v") : $item;
            }, $this->request->all());
        // array_map(function ($value) {
        //     return is_string($value) ? trim($value, " \n\r\t\v") : $value;
        // }, $this->request->all())

    }
}
