<?php
declare(strict_types = 1);

namespace Psancho\Comic\Model\Book;

use Override;
use Psancho\Galeizon\Model\Database\Filter as DatabaseFilter;
use Psancho\Galeizon\Model\Database\Paging;

class Filter extends DatabaseFilter
{

    /** @var list<string> */
    protected array $clauseList = [];
    /** @var array<string, scalar> */
    public protected(set) array $paramList = [];

    protected static array $columnList = ['series', 'number', 'title'];
    /** @var array<string, string> */
    protected static array $replaceSorts = [
        'series' => 'S.title',
        'number' => 'B.`number`',
        'title' => 'B.title',
    ];

    public function __construct(
        public ?Paging $paging = null,
        /** @var array<string> */
        public array $sort = [],
        public ?string $q = null,
        public ?string $series = null,
        public ?string $title = null,
        public ?string $publisher = null,
        public ?string $author = null,
        public ?string $contribution = null,
        public ?bool $owned = null,
        public ?bool $authorSeries = null,
        public ?bool $biopic = null,
    )
    {
        parent::__construct($paging, $sort);
    }


    #[Override]
    protected function setClauseWhere(): self
    {
        if (!is_null($this->q)) {
            array_push($this->clauseList, <<<SQL
                (S.title like (@q := :q) or B.title like @q or P.`name` like @q)
            SQL);
            $this->paramList[':q'] = '%' . $this->q . '%';
        }
        if (!is_null($this->series)) {
            $this->clauseList[] = 'S.title like :series';
            $this->paramList[':series'] = '%' . $this->series . '%';
        }
        if (!is_null($this->title)) {
            $this->clauseList[] = 'B.title like :title';
            $this->paramList[':title'] = '%' . $this->title . '%';
        }
        if (!is_null($this->publisher)) {
            $this->clauseList[] = 'P.`name` like :publisher';
            $this->paramList[':publisher'] = '%' . $this->publisher . '%';
        }
        if (!is_null($this->author)) {
            $this->clauseList[] = 'A.`name` like :author';
            $this->paramList[':author'] = '%' . $this->author . '%';
        }
        if (!is_null($this->contribution)) {
            $this->clauseList[] = 'BA.contribution like :contribution';
            $this->paramList[':contribution'] = '%' . $this->contribution . '%';
        }
        if (!is_null($this->owned)) {
            array_push($this->clauseList, sprintf("flags & %d = :owned", Flag::Owned->value));
            $this->paramList[':owned'] = $this->owned ? Flag::Owned->value : 0;
        }
        if (!is_null($this->authorSeries)) {
            array_push($this->clauseList, sprintf("flags & %d = :authorSeries", Flag::AuthorSeries->value));
            $this->paramList[':authorSeries'] = $this->authorSeries ? Flag::AuthorSeries->value : 0;
        }
        if (!is_null($this->biopic)) {
            array_push($this->clauseList, sprintf("flags & %d = :biopic", Flag::Biopic->value));
            $this->paramList[':biopic'] = $this->biopic ? Flag::Biopic->value : 0;
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
        return "\norder by " . (count($this->sort) > 0
        ? implode(', ', $this->sort)
        : "S.title, B.`number`, B.title");
    }

    #[Override]
    public function limit(): string
    {
        return is_null($this->paging) ? ""
        : sprintf("\nlimit %d, %d", $this->paging->offset, $this->paging->perPage);
    }
}
