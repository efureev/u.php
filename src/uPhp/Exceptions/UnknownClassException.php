<?php

namespace uPhp\Exceptions;

/**
 * UnknownClassException represents an exception caused by using an unknown class.
 */
class UnknownClassException extends \Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Unknown Class';
    }
}
