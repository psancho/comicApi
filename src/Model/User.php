<?php
declare(strict_types = 1);

namespace Psancho\Comic\Model;

use Override;
use PDO;
use PDOException;
use Psancho\Comic\Model\User\Filter;
use Psancho\Comic\Model\User\Flag;
use Psancho\Galeizon\App;
use Psancho\Galeizon\Model\Auth\DuplicateUserException;
use Psancho\Galeizon\Model\Auth\UserIdentity;

class User extends UserIdentity
{
    public bool $active = false;
    public protected(set) int $id = 0;

    protected const string SQL_SELECT = <<<SQL
    select
        id,
        username,
        firstname,
        lastname,
        email,
        flags &
    SQL
    . ' ' . Flag::Active->value . ' '
    . <<<SQL
    as active
    from userprofile
    SQL;

    #[Override]
    public function register(): static
    {
        try {
            $this->create();
        } catch (DuplicateUserException) {
            $existing = self::retrieveByUsername($this->username);
            if (is_null($existing) || $this->email !== $existing->email) {
                throw new DuplicateUserException("DUPLICATE_EMAIL_OR_USERNAME");
            }
        }

        $flags = 0;
        if ($this->active) {
            $flags = Flag::Active->value;
        }

        $sql = <<<SQL
        insert into userprofile (username, firstname, lastname, email, flags)
        values (:username, :firstname, :lastname, :email, :flags) as R
        on duplicate key update
            firstname = R.firstname,
            lastname = R.lastname,
            email = R.email,
            flags = R.flags
        SQL;
        $stmt = App::getInstance()->dataCnx->prepare($sql) ?: throw new PDOException("DB_ERROR");
        $stmt->bindValue(":username", $this->username);
        $stmt->bindValue(":firstname", $this->firstname);
        $stmt->bindValue(":lastname", $this->lastname);
        $stmt->bindValue(":email", $this->email);
        $stmt->bindValue(":flags", $flags);
        $stmt->execute();

        return $this;
    }

    #[Override]
    public function isRegistered(): bool
    {
        $registered = self::retrieveByEmail($this->email);

        if (is_null($registered)) {
            $this->register();
            return false;
        } else {
            return true;
        }
    }

    #[Override]
    public function isActive(): bool
    {
        $flagActive = Flag::Active->value;
        $sql = <<<SQL
        select flags & $flagActive from userprofile where username = ?
        SQL;
        $stmt = App::getInstance()->dataCnx->prepare($sql) ?: throw new PDOException("DB_ERROR");
        $stmt->setFetchMode(PDO::FETCH_COLUMN, 0);
        $stmt->execute([$this->username]);
        $active = $stmt->fetch();
        $stmt->closeCursor();

        $internal = parent::isActive();
        return $internal && $active === Flag::Active->value;
    }

    #[Override]
    public function update(UserIdentity $targetUser): static
    {
        parent::update($targetUser);

        $sql = <<<SQL
        update userprofile
        set username = :username, `firstname` = :firstName, `lastname` = :lastName,
        `email` = :email, `flags` = :flags
        where username = :W_username or email = :W_email
        SQL;
        $stmt = App::getInstance()->dataCnx->prepare($sql) ?: throw new PDOException("DB_ERROR");
        $stmt->bindValue(":firstName", $this->firstname ?? $targetUser->firstname);
        $stmt->bindValue(":lastName", $this->lastname ?? $targetUser->lastname);
        $stmt->bindValue(":email", $this->email);
        $stmt->bindValue(":username", $this->username ?? $targetUser->username);
        $stmt->bindValue(":W_email", $this->email);
        $stmt->bindValue(":W_username", $this->username ?? $targetUser->username);
        $stmt->bindValue(":flags", $this->active ? Flag::Active->value : 0);
        $stmt->execute();

        return $this;
    }

    public static function retrieveByEmail(string $email): ?self
    {
        $sql = self::SQL_SELECT . <<<SQL

        where email = ?
        SQL;
        $stmt = App::getInstance()->dataCnx->prepare($sql) ?: throw new PDOException("DB_ERROR");
        $stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        $stmt->execute([$email]);
        /** @var ?self $user */
        $user = $stmt->fetch() ?: null;
        $stmt->closeCursor();

        return $user;
    }

    /** @return static */
    public static function retrieveById(int $id): ?self
    {
        $sql = self::SQL_SELECT . <<<SQL

        where id = ?
        SQL;
        $stmt = App::getInstance()->dataCnx->prepare($sql) ?: throw new PDOException("DB_ERROR");
        $stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        $stmt->execute([$id]);
        $user = $stmt->fetch() ?: null;
        assert(is_null($user) || $user instanceof static);
        $stmt->closeCursor();

        return $user;
    }

    #[Override]
    public function updateLastAccess(): static
    {
        $sql = <<<SQL
        update userprofile set last_access = now() where email = :email
        SQL;
        $stmt = App::getInstance()->dataCnx->prepare($sql) ?: throw new PDOException("DB_ERROR");
        $stmt->bindValue(":email", $this->email);
        $stmt->execute();

        return $this;
    }
    #[Override]
    public function retrieveDecorated(): static
    {
        $found = static::retrieveByEmail($this->email);

        if (is_null($found)) {
            return $this;
        } else {
            $this->active = $found->active;
            $this->id = $found->id;

            return $this;
        }
    }

    public static function delete(string $email): bool
    {
        $sql = <<<SQL
        delete from `userprofile`
        where email = ?
        SQL;
        $stmt = App::getInstance()->dataCnx->prepare($sql) ?: throw new PDOException("DB_ERROR");
        $stmt->execute([$email]);
        $count = $stmt->rowCount();

        return $count !== 0;
    }

    public static function countData(Filter $filter): int
    {
        $select = <<<SQL
        select count(id) from `userprofile`
        SQL;

        $sql = $select . $filter->where();

        $cnx = App::getInstance()->dataCnx;
        $stmt = $cnx->prepare($sql) ?: throw new PDOException("DB_ERROR");
        $stmt->setFetchMode(PDO::FETCH_COLUMN, 0);
        $stmt->execute($filter->paramList);
        $count = $stmt->fetch() ?: 0;
        assert(is_int($count));
        $stmt->closeCursor();

        return $count;
    }

    /** @return list<self> */
    public static function getList(Filter $filter): array
    {
        $sql = self::SQL_SELECT . $filter->where() . $filter->orderBy() . $filter->limit();

        $cnx = App::getInstance()->dataCnx;
        $stmt = $cnx->prepare($sql) ?: throw new PDOException("DB_ERROR");
        $stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
        $stmt->execute($filter->paramList);
        /** @var list<self> $list */
        $list = $stmt->fetchAll() ?: [];
        $stmt->closeCursor();
        return $list;
    }

    public static function fromObject(object $object): self
    {
        $typed = new self;
        if (property_exists($object, 'username') && is_scalar($object->username)) {
            $typed->username = trim((string) $object->username);
        }
        if (property_exists($object, 'firstname') && is_scalar($object->firstname)) {
            $typed->firstname = trim((string) $object->firstname);
        }
        if (property_exists($object, 'lastname') && is_scalar($object->lastname)) {
            $typed->lastname = trim((string) $object->lastname);
        }
        if (property_exists($object, 'email') && is_scalar($object->email)) {
            $typed->email = trim((string) $object->email);
        }
        if (property_exists($object, 'active') && is_scalar($object->active)) {
            $typed->active = (bool) $object->active;
        }

        return $typed;
    }
}
