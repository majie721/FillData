<?php


namespace FillData\LaravelFill;


use FillData\Interfaces\LaravelRequest;
use FillData\ToObject;
use FillData\Traits\LaravelRequestValidationTrait;
use Illuminate\Support\Facades\Validator;

class ValidateRequest extends ToObject implements LaravelRequest
{

    use LaravelRequestValidationTrait;

    protected bool $stopOnFirstFailure = false;

    public function __construct(){
        $data = $this->validateData();
        $this->prepareForValidation($data);
        $validator = Validator::make($data, $this->rules(),$this->messages(),$this->attributes())
            ->stopOnFirstFailure($this->stopOnFirstFailure);
        
        if(method_exists($this,'validatorAfter')){
            $validator->after([$this,'validatorAfter']);
        }

        if($validator->fails()){
           dd($validator->errors(),$validator->errors()->all());
        }

        parent::__construct();
    }


    /**
     * @param $validator \Illuminate\Contracts\Validation\Validator
     * @link
     */
    public function validatorAfter($validator){

    }


}