<?php
declare(strict_types = 1);

namespace Psancho\Comic\Model;

enum Flag: int
{
    case Active = 1;
    case Deleted = 1 << 1;
}
