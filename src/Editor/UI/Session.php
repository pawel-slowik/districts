<?php

declare(strict_types=1);

namespace Districts\Editor\UI;

use SlimSession\Helper;

class Session extends Helper
{
    public function getAndDelete(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);
        $this->delete($key);
        return $value;
    }
}
