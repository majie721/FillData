<?php


namespace FillData\Traits;


trait LaravelRequestValidationTrait
{
    private  $validateData;

  

    protected function validateData():array{
        return \request()->all();
    }

    /**
     * laravel rules
     * @return array
     */
    public function rules(){
        return [];
    }

    /**
     * laravel messages
     * @return array
     */
    public function messages(){
        return [];
    }
    /**
     * laravel attributes
     * @return array
     */
    public function attributes(){
        return [];
    }

    /**
     * @param array $data 检验的数据
     * @link
     */
    public function prepareForValidation(array &$data = []){
        
    }

}