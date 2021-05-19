<?php

namespace holoyan\EloquentFilter\Rules;

class DateRule extends FilterRule
{
    /**
     * @var array
     */
    protected $date = [];

    /**
     * @param string $filterKey
     * @param mixed $filterValue
     */
    public function handle(string $filterKey, $filterValue): void
    {
        if (isset($this->date['from'])) {
            $this->builder->where($this->getColumn($filterKey), $this->date['from']['equal'] ? '>=' : '>', $this->date['from']['value']);
        }

        if (isset($this->date['to'])) {
            $this->builder->where($this->getColumn($filterKey), $this->date['to']['equal'] ? '<=' : '<', $this->date['to']['value']);
        }
    }

    /**
     * @param $from
     * @param  bool  $equal
     */
    public function from($from, bool $equal = true)
    {
        $this->date['from'] = [
            'value' => $from,
            'equal' => $equal
        ];
    }

    /**
     * @param $to
     * @param  bool  $equal
     */
    public function to($to, bool $equal = true)
    {
        $this->date['to'] = [
            'value' => $to,
            'equal' => $equal
        ];
    }

}
