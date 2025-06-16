<?php
declare(strict_types = 1);

namespace Psancho\Comic\Model;

use PDO;
use PDOException;
use Psancho\Galeizon\App;

class Author
{
    public string $name = '';
    public string $contribution = '';

    /** @return list<self> */
    public static function findAuthors(int $bookId): array
    {
        $sql = <<<SQL
        select
            BA.contribution,
            A.`name`
        from  book_author BA
        join author A on A.id = BA.author_id
        where BA.book_id = ?
    SQL;;
        $stmt = App::getInstance()->dataCnx->prepare($sql) ?: throw new PDOException("DB_ERROR");
        $stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        $stmt->execute([$bookId]);
        /** @var list<self> $authors */
        $authors = $stmt->fetchAll() ?: [];
        $stmt->closeCursor();

        return $authors;
    }
}
