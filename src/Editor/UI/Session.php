<?php

declare(strict_types=1);

namespace Districts\Editor\UI;

use SlimSession\Helper;

final class Session
{
    /**
     * @param Helper<mixed> $helper
     */
    public function __construct(
        private Helper $helper,
    ) {
    }

    public function set(string $key, mixed $value): void
    {
        $this->helper->set($key, $value);
    }

    public function getAndDelete(string $key, mixed $default = null): mixed
    {
        $value = $this->helper->get($key, $default);
        $this->helper->delete($key);
        return $value;
    }

    public function delete(string $key): void
    {
        $this->helper->delete($key);
    }
}
