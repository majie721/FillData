<?php


namespace FillData\Exceptions;


class ValidateException extends \Exception
{
    public function __construct($message,string $field,$code = 0, Throwable $previous = null)
    {
        
        parent::__construct($message, $code, $previous);
    }
}