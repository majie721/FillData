<?php
namespace FillData\Test;

use FillData\Test\Example\ExampleBean;
use FillData\Test\Example\UserBean;
use FillData\Traits\FillDataTrait;

class TestCase extends \PHPUnit\Framework\TestCase
{


    public function testExampleBean(){
        $data = [
            'id' => '2',
            'name' => 1222,
            'classes' => ["en",'ch','fr'],
        ];

        $obj = new ExampleBean($data);
        var_dump($obj);
    }


    public function testExampleBean1(){
        $data = [
            'id' => [],
            'name' => 1222,
            'classes' => ["en",'ch','fr'],
        ];

        $obj = new ExampleBean($data);
        var_dump($obj);
    }

    public function testExampleBean2(){
        $data = [
            'id' => 1,
            'name' => [],
            'classes' => ["en",'ch','fr'],
        ];

        $obj = new ExampleBean($data);
        var_dump($obj);
    }

    public function testExampleBean3(){
        $data = [
            'id' => 1.89,
            'name' => 12.22,
            'classes' => ["en",'ch','fr'],
        ];

        $obj = new ExampleBean($data);
        var_dump($obj);
    }

    public function testExampleBean4(){
        $data = [
            'id' => null,
            'name' => 12.22,
            'classes' => ["en",'ch','fr'],
        ];

        $obj = new ExampleBean($data);
        var_dump($obj);
    }

    public function testToArray(){
        $data = [
            'id' => '2',
            'name' => 1222,
            'classes' => ["en",'ch','fr'],
        ];

        $obj = new ExampleBean($data);

        var_dump($obj->toArray());
    }


    public function testToJson(){
        $data = [
            'id' => '2',
            'name' => 1222,
            'classes' => ["en",'ch','fr'],
        ];

        $obj = new ExampleBean($data);

        var_dump($obj->toJson());
    }

    public function testFromBean(){
        $data = [
            'id' => '2',
            'name' => 1222,
            'classes' => ["en",'ch','fr'],
        ];
        $res =  ExampleBean::fromItem($data);

        var_dump($res);
    }


    public function testFromList(){
        $data = [
            [
                'id' => '2',
                'name' => 222,
                'classes' => ["en",'ch','fr'],
            ],
            [
                'id' => '3',
                'name' => 333,
                'classes' => ["en3",'ch3','fr3'],
            ]
        ];
        $res =  ExampleBean::fromList($data);

        var_dump($res);
    }


    public function testUser(){
        $user = [
            'name'=>'hehe',
            'age'=>12,
            'sex'=>'man',
            'job'=>[
                'name'=>'student',
                'tools'=>['en','office']
            ]
        ];

        var_dump(new UserBean($user));
    }

}