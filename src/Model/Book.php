<?php
declare(strict_types = 1);

namespace Psancho\Comic\Model;

use PDO;
use PDOException;
use PDOStatement;
use Psancho\Comic\Model\Book\Filter;
use Psancho\Comic\Model\Book\Flag;
use Psancho\Galeizon\App;

class Book
{
    public int $id = 0;
    public ?string $series = null;
    public ?int $number = null;
    public string $title = '';
    public bool $owned {
        get {
            return ($this->flags & Flag::Owned->value) !== 0;
        }
        set(bool $owned) {
            if ($owned) {
                $this->flags |= Flag::Owned->value;
            } else {
                $this->flags &= ~Flag::Owned->value;
            }
        }
    }
    public bool $authorSeries {
        get {
            return ($this->flags & Flag::AuthorSeries->value) !== 0;
        }
        set(bool $authorSeries) {
            if ($authorSeries) {
                $this->flags |= Flag::AuthorSeries->value;
            } else {
                $this->flags &= ~Flag::AuthorSeries->value;
            }
        }
    }
    public bool $biopic {
        get {
            return ($this->flags & Flag::Biopic->value) !== 0;
        }
        set(bool $biopic) {
            if ($biopic) {
                $this->flags |= Flag::Biopic->value;
            } else {
                $this->flags &= ~Flag::Biopic->value;
            }
        }
    }
    public string $publisher = '';
    /** @var list<Author> */
    public array $authors;
    private int $flags = 0;

    public function __construct()
    {
        $this->authors = Author::findAuthors($this->id);
    }

    public static function first(Filter $filter): ?self
    {
        $stmt = self::stmtFind($filter);
        /** @var ?self $book */
        $book = $stmt->fetch() ?: null;
        $stmt->closeCursor();

        return $book;
    }

    /** @return list<self> */
    public static function list(Filter $filter): array
    {
        $stmt = self::stmtFind($filter);
        /** @var list<self> $books */
        $books = $stmt->fetchAll() ?: [];
        $stmt->closeCursor();

        return $books;
    }

    public static function count(Filter $filter): int
    {
        $select = <<<SQL
        select count(distinct B.id)
        from book B
        left join series S on S.id = B.series_id
        left join publisher P on P.id = B.publisher_id
        left join book_author BA on BA.book_id = B.id
        left join author A on A.id = BA.author_id
        SQL;
        $sql = $select . $filter->where();
        $stmt = App::getInstance()->dataCnx->prepare($sql) ?: throw new PDOException("DB_ERROR");
        $stmt->setFetchMode(PDO::FETCH_COLUMN, 0);
        $stmt->execute($filter->paramList);
        /** @var int $count */
        $count = $stmt->fetch() ?: 0;
        $stmt->closeCursor();

        return $count;
    }

    private static function stmtFind(Filter $filter): PDOStatement
    {
        $select = <<<SQL
        select
            S.title as series,
            B.id,
            B.`number`,
            B.title,
            B.flags,
            P.`name` as publisher
        from book B
        left join series S on S.id = B.series_id
        left join publisher P on P.id = B.publisher_id
        left join book_author BA on BA.book_id = B.id
        left join author A on A.id = BA.author_id
        SQL;
        $groupBy = <<<SQL
        \ngroup by B.id
        SQL;
        $sql = $select . $filter->where() . $groupBy . $filter->orderBy() . $filter->limit();
        $stmt = App::getInstance()->dataCnx->prepare($sql) ?: throw new PDOException("DB_ERROR");
        $stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        $stmt->execute($filter->paramList);

        return $stmt;
    }
}
