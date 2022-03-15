<?php

namespace Atikercan\LaravelRepositorier;

use Illuminate\Database\Eloquent\Builder;

class Scope
{
    /**
     * @var string[]
     */
    private array $types = [ 'where', 'orWhere', 'whereNull', 'orWhereNull', 'whereHas', 'orWhereHas', 'whereDoesntHave', 'orWhereDoesntHave', 'has', 'orHas', 'hasNested', 'orHasNested' ];

    /**
     * @var array
     */
    protected array $scopes = [];

    /**
     * @param array $filters
     */
    public function __construct(array $filters = []) {
        foreach($filters as $params) {
            $type = 'where';
            if(in_array($params[0], $this->types, true)) {
                $type = array_shift($params);
            }
            $this->addScope($type, $params);
        }
    }

    /**
     * Applies filters to a query
     * @param Builder $query
     */
    public function apply(Builder &$query):void
    {
        foreach($this->getScopes() as $scope) {
            call_user_func_array( array($query, $scope['type']), $scope['params']);
        }
    }

    /**
     * @return array
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * @param array $scopes
     */
    public function setScopes(array $scopes): void
    {
        $this->scopes = $scopes;
    }

    /**
     * Adds a filter to filters array
     * @param string $type
     * @param mixed $params
     * @return void
     */
    protected function addScope(string $type, $params):void
    {
        $filters = $this->getScopes();
        $filters[] = [
            'type' => $type,
            'params' => $params
        ];
        $this->setScopes($filters);
    }

    /**
     * @param $method
     * @param mixed $params
     * @return Scope
     */
    public function __call($method, $params) {
        if(in_array($method, $this->types, true)) {
            $this->addScope($method, $params);
        }

        return $this;
    }
}
