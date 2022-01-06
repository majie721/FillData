# FillData
将php 数组转换成对象 

### Laravel Request 
> 控制器的入参Bean Class 继承 ValidateRequest 用于校验请求参数

>不需要检验的类继承 ToObject 对象即可


##### Controller example:
```
class IndexController extends Controller
{
    public function test(AddImportCountryBean $bean){
        dd($bean);
    }
}  
```

##### Beans example:
```
<?php


namespace App\Http\Beans;


use FillData\LaravelFill\ValidateRequest;

class AddImportCountryBean  extends ValidateRequest
{
    /**
     * @var \App\Http\Beans\AddImportCountryItem[] Destination Country Info
     */
    public array $product_country;


    public function rules()
    {
        return [
            'product_country.*.product_sku'=>'required',
            'product_country.*.import_country_data.*.country_code'=>'required',
            'product_country.*.import_country_data.*.declared_value'=>'required|numeric|min:100',

        ];
    }

    public function messages()
    {
        return ['product_country.*.product_sku.required'=>':attribute 必填'];
    }

    public function attributes()
    {
        return [
            'product_country.*.product_sku'=>'进口国的sku'
        ];
    }
}

```
