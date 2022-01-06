<?php


namespace FillData\Exceptions;

use \Illuminate\Support\MessageBag;

class LaravelValidateException extends \Exception
{
    private MessageBag $errors;

    public function __construct($message, $errors,$code = 0, Throwable $previous = null)
    {

        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}