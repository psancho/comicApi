<?php
declare(strict_types = 1);

namespace Psancho\Comic\Model\Book;

enum Flag: int
{
    case Owned = 1;
    case AuthorSeries = 1 << 1;
    case Biopic = 1 << 2;
}
