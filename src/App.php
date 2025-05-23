<?php
declare(strict_types=1);

namespace Psancho\Comic;

use PDO;
use Psancho\Comic\Pattern\Singleton;

require_once dirname(__DIR__) . '/vendor/autoload.php';

class App extends Singleton
{
    public string $caca = '';

    // public private(set) PDO $pdo; // @_phpstan-ignore property.uninitialized

    #[\Override]
    protected function init(): void
    {
        self::promoteErrorsToExceptions();
        // $this->pdo = new PDO();
    }

    private static function promoteErrorsToExceptions(): void
    {
        set_error_handler(function (int $errNo, string $errStr, string $errFile, int $errLine): bool
        {
            // une exception dans un destructeur ça ne fait pas propre, donc je gère
            $backtrace = debug_backtrace();
            $e = new \ErrorException($errStr, 0, $errNo, $errFile, $errLine);
            if (array_key_exists(2, $backtrace)
                && '__destruct' === $backtrace[2]['function']
            ) {
                // LogProvider::error($e);
                echo 'Configuration issue: check log.';
            } else {
                throw $e;
            }

            return true;
        });
    }
}
