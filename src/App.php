<?php
declare(strict_types=1);

namespace Psancho\Comic;

use PDO;
use Psancho\Comic\Model\Conf;
use Psancho\Comic\Model\Database\Connection;
use Psancho\Comic\Pattern\Singleton;

require_once dirname(__DIR__) . '/vendor/autoload.php';

class App extends Singleton
{
    public protected(set) Conf $conf;// @phpstan-ignore property.uninitialized
    public protected(set) PDO $dbCnx;// @phpstan-ignore property.uninitialized

    #[\Override]
    protected function build(): void
    {
        $this->conf = Conf::getInstance();
        $this->dbCnx = Connection::getInstance($this->conf->database);
    }
}
