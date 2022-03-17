<?php

namespace Atikercan\LaravelRepositorier;

use Illuminate\Database\Eloquent\Builder;

class Criteria
{
    /**
     * @var string[]
     */
    private $filterTypes = [ 'where', 'whereIn', 'orWhere', 'whereNull', 'orWhereNull', 'whereHas', 'orWhereHas', 'whereDoesntHave', 'orWhereDoesntHave', 'has', 'orHas', 'hasNested', 'orHasNested' ];

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var string
     */
    protected $type = 'where';

    /**
     * @param array $filters
     */
    public function __construct(array $filters = []) {
        foreach($filters as $params) {
            $type = 'where';
            if(in_array($params[0], $this->filterTypes, true)) {
                $type = array_shift($params);
            }
            $this->addFilter($type, $params);
        }
    }

    /**
     * Applies filters to a query
     * @param Builder $query
     */
    public function apply(Builder &$query) {
        foreach($this->getFilters() as $filter) {
            call_user_func_array( array($query, $filter['type']), $filter['params']);
        }
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param array $filters
     */
    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    /**
     * Adds a filter to filters array
     * @param string $type
     * @param mixed $params
     * @return void
     */
    protected function addFilter(string $type, $params) {
        $filters = $this->getFilters();
        $filters[] = [
            'type' => $type,
            'params' => $params
        ];
        $this->setFilters($filters);
    }

    /**
     * @param $method
     * @param mixed $params
     * @return Criteria
     */
    public function __call($method, $params) {
        if(in_array($method, $this->filterTypes)) {
            $this->addFilter($method, $params);
        }

        return $this;
    }
}
