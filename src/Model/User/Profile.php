<?php
declare(strict_types = 1);

namespace Psancho\Comic\Model\User;


enum Profile: int
{
    case Simple = 1;
    case Admin = 1 << 1;
}
