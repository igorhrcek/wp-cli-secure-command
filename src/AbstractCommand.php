<?php

namespace WP_CLI_Secure;

abstract class AbstractCommand implements CommandInterface {
    /**
     * @return array
     */
    public function getSynopsis(): array
    {
        return [];
    }

    /**
     * @return string
     */
    final public function getName(): string
    {
        return sprintf('benchmark %s', $this->getCommandName());
    }

    /**
     * Get the full command name
     *
     * @return string
     */
    abstract protected function getCommandName() : string;
}
