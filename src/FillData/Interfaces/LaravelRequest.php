<?php


namespace FillData\Interfaces;


interface LaravelRequest
{
    /**
     * laravel rules
     * @return array
     */
    public function rules();

    /**
     * laravel messages
     * @return array
     */
    public function messages();

    /**
     * laravel attributes
     * @return array
     */
    public function attributes();

    /**
     * laravel prepareForValidation()
     * @return mixed
     */
    public function prepareForValidation();
}