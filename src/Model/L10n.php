<?php
declare(strict_types = 1);

namespace Psancho\Comic\Model;

use PDO;
use PDOException;
use Psancho\Galeizon\App;
use Psancho\Galeizon\Model\Locale;

class L10n
{
    public static string $defaultLocale = 'fr-FR';
    protected string $selectedLocale = '';
    /** @var ?array<string, string> */
    private ?array $_translations = null;

    /** @param string|list<string> $locales */
    public function __construct(string|array $locales, protected string $scope)
    {
        if (is_string($locales)) {
            $locales = [$locales];
        }
        $this->selectedLocale = self::selectLocale($locales, $scope);
    }

    public function getSelectedLocale(): string
    {
        return $this->selectedLocale;
    }

    /** @return array<string, string> */
    public function export(): array
    {
        return $this->getTranslations();
    }

    /** @return array<string, string> */
    protected function getTranslations(): array
    {
        if (!is_array($this->_translations)) {
            $sql = <<<SQL
            select `key`, label from l10n
            where find_in_set(?, scope) and locale = replace(?, '_', '-')
            order by `key`
            SQL;
            $stmt = App::getInstance()->dataCnx->prepare($sql) ?: throw new PDOException("DB_ERROR");
            $stmt->setFetchMode(PDO::FETCH_NUM);
            $stmt->execute([$this->scope, $this->selectedLocale]);
            $stmt->bindColumn(1, $key);
            $stmt->bindColumn(2, $label);
            $this->_translations = [];
            while ($stmt->fetch()) {
                assert(is_string($key) && is_string($label));
                $this->_translations[$key] = $label;
            }
            //Si pas de trad, on prends la langue par défaut
            if(count($this->_translations) === 0) {
                $stmt->execute([$this->scope, self::$defaultLocale]);
                while ($stmt->fetch()) {
                    assert(is_string($key) && is_string($label));
                    $this->_translations[$key] = $label;
                }
            }
            $stmt->closeCursor();
        }

        return $this->_translations;
    }

    public function label(string $key, ?string $default = null): string
    {
        return $this->getTranslations()[$key] ?? $default ?? $key;
    }

    /** @param list<string> $acceptedLocales */
    public static function selectLocale(array $acceptedLocales, string $scope): string
    {
        $sql = <<<SQL
        select distinct L.locale
        from l10n L
        where find_in_set(?, L.scope)
        SQL;
        $stmt = App::getInstance()->dataCnx->prepare($sql) ?: throw new PDOException("DB_ERROR");
        $stmt->setFetchMode(PDO::FETCH_COLUMN, 0);
        $stmt->execute([$scope]);
        /** @var list<string> $availableLocales */
        $availableLocales = $stmt->fetchAll() ?: [];
        $stmt->closeCursor();

        return Locale::chooseLocale($acceptedLocales, $availableLocales, self::$defaultLocale);
    }
}
