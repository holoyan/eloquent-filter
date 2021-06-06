<?php


namespace holoyan\EloquentFilter\Rules;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;

class SimpleRule extends FilterRule
{
    public const DEFAULT_OPERATOR = '=';

    public const LIKE_OPERATOR = 'like';

    public const LIKE_COMPARISON_TYPES = [
        'left' => 1,
        'right' => 2,
        'both' => 3
    ];

    /**
     * @var string
     */
    private $operator;

    /**
     * @var int
     */
    private $comparisonType;

    /**
     * @inheritDoc
     */
    public function handle(string $filterKey, $filterValue): void
    {
        if (is_array($filterValue)) {
            $this->builder->whereIn($this->getColumn($filterKey), $this->getValue($filterValue));
        } else {
            $this->builder->where($this->getColumn($filterKey), $this->getOperator(), $this->getValue($filterValue));
        }
    }

    /**
     * @param string $operator
     * @return $this
     */
    public function setOperator(string $operator): self
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * @param int $comparisonType
     * @return SimpleRule
     */
    public function setComparisonType(int $comparisonType): SimpleRule
    {
        $this->comparisonType = $comparisonType;

        return $this;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator ?? self::DEFAULT_OPERATOR;
    }

    /**
     * @return $this
     */
    public function startsWith()
    {
        $this->setOperator(self::LIKE_OPERATOR)->setComparisonType(self::LIKE_COMPARISON_TYPES['left']);
        return $this;
    }

    /**
     * @return $this
     */
    public function endsWith()
    {
        $this->setOperator(self::LIKE_OPERATOR)->setComparisonType(self::LIKE_COMPARISON_TYPES['right']);
        return $this;
    }

    /**
     * @return $this
     */
    public function contains()
    {
        $this->setOperator(self::LIKE_OPERATOR)->setComparisonType(self::LIKE_COMPARISON_TYPES['both']);
        return $this;
    }

    /**
     * @return $this
     */
    public function exactMatch()
    {
        $this->operator = self::DEFAULT_OPERATOR;
        return $this;
    }


    /**
     * @param mixed $filterValue
     * @return mixed
     */
    public function getValue($filterValue)
    {
        if ($this->isLikeComparison()) {
            switch ($this->comparisonType) {
                case self::LIKE_COMPARISON_TYPES['left'] : {
                    return "%{$filterValue}";
                }
                case self::LIKE_COMPARISON_TYPES['right'] : {
                    return "{$filterValue}%";
                }
                case self::LIKE_COMPARISON_TYPES['both'] : {
                    return "%{$filterValue}%";
                }
                default:
                    return "%{$filterValue}%";
            }
        }

        return $filterValue;
    }

    private function isLikeComparison(): bool
    {
        return $this->operator == self::LIKE_OPERATOR;
    }
}
