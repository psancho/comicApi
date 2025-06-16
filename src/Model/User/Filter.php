<?php
declare(strict_types = 1);

namespace Psancho\Comic\Model\User;

use Override;
use Psancho\Galeizon\Model\Database\Filter as DatabaseFilter;
use Psancho\Galeizon\Model\Database\Paging;

class Filter extends DatabaseFilter
{

    /** @var list<string> */
    protected array $clauseList = [];
    /** @var array<string, scalar> */
    public protected(set) array $paramList = [];

    protected static array $columnList = ['firstname', 'lastname', 'email', 'last_access'];

    public function __construct(
        public ?Paging $paging = null,
        /** @var array<string> */
        public array $sort = [],
        public ?string $email = null,
        public ?string $name = null,
        public ?bool $active = null,

    )
    {
        parent::__construct($paging, $sort);
    }

    #[Override]
    protected function setClauseWhere(): self
    {
        if (!is_null($this->email)) {
            array_push($this->clauseList, "email like :email");
            $this->paramList[':email'] = '%' . $this->email . '%';
        }
        if (!is_null($this->name)) {
            $this->clauseList[] = '(firstname like (@name := :name) or lastname like @name or email like @name)';
            $this->paramList[':name'] = '%' . $this->name . '%';
        }
        if (!is_null($this->active)) {
            array_push($this->clauseList, sprintf("flags & %d = :active", Flag::Active->value));
            $this->paramList[':active'] = $this->active ? Flag::Active->value : 0;
        }

        return $this;
    }

    #[Override]
    public function where(): string
    {
        if (count($this->clauseList) === 0) {
            return "";
        }

        return "\nwhere " . implode(' and ', $this->clauseList);
    }

    #[Override]
    public function orderBy(): string
    {
        return "\norder by " . count($this->sort) > 0
        ? implode(', ', $this->sort)
        : "lastname, firstName";
    }

    #[Override]
    public function limit(): string
    {
        return is_null($this->paging) ? ""
        : sprintf("\nlimit %d, %d", $this->paging->offset, $this->paging->perPage);
    }
}
