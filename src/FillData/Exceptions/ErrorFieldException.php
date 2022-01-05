<?php


namespace FillData\Exceptions;


use Throwable;

class ErrorFieldException extends \Exception
{
    private string $field;

    public function __construct($message,string $field,$code = 0, Throwable $previous = null)
    {
        $this->field = $field;
        parent::__construct($message, $code, $previous);
    }

    public function getField(): string
    {
        return $this->field;
    }
}