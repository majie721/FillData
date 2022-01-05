<?php


namespace FillData;


use FillData\Exceptions\ErrorFieldException;
use FillData\Interfaces\ArrayAble;

class PropertyDocParser
{
    /**
     * 文档属性类型
     *
     * @var array
     */
    protected static array $propertyTypes = [];

    /** @var object 要设置的类 */
    protected object $class;

    /** @var string 类名 */
    protected string $className;

    public function __construct(object $class)
    {
        $this->class = $class;
        $this->className = get_class($class);
    }

    /**
     * 根据属性类型填充数据
     * @param array|object $data
     * @param bool $smart true时会自动将不含数字key的一位数组转为多维数组(如果属性是多维对象数组的话), 多用于XML解析时单个和多个元素解析的结构不同
     * @param array $parentProperties
     * @return void
     * @throws \ErrorException
     * @throws \FillData\Exceptions\ErrorFieldException
     * @throws \ReflectionException
     */
    public function fillTypeData($data = [], bool $smart = false, array $parentProperties = []): void
    {
        if (!$data) {
            return;
        }

        if ($data instanceof ArrayAble) {
            $data = $data->toArray();
        }

        $propertyTypes = $this->getPropertyTypes();
        foreach ($data as $propertyName => $propertyValue) {
            //获取完整的属性名称(包含父属性)
            $fullProperties = [...$parentProperties, $propertyName];
            $fullPropertyName = $fullProperties ? implode('.', $fullProperties) : $propertyName;
            try {
                if (!array_key_exists($propertyName, $propertyTypes)) {
                    continue;
                }

                $reflectionProperty = new \ReflectionProperty($this->class, $propertyName);
                if (!$reflectionProperty->isPublic()) {
                    continue;
                }

                $property = $propertyTypes[$propertyName];
                //如果没有包含类对象的话则直接设置值
                if (!$property['is_class']) {
                    //直接设置属性, 不允许为空的不设置
                    if ($propertyValue === null && $reflectionProperty->hasType() && !$reflectionProperty->getType()->allowsNull()) {
                        throw new ErrorFieldException( sprintf("%s can't be null",$fullPropertyName),$fullPropertyName);
                    }

                    $this->setPropertyValue($propertyName, $propertyValue);
                    continue;
                }

                if ($property['is_collection']) {
                    //如果解析的文档对象是个数组的话, 循环设置值
                    if (is_array($propertyValue) || is_object($propertyValue)) {
                        //将不含数字key的一位数组转为多维数组, 多用于XML解析时单个和多个元素解析的结构不同
                        if ($smart && $propertyValue && !isset($propertyValue[0]) && !isset($propertyValue->{0})) {
                            $propertyValue = [$propertyValue];
                        }

                        $values = [];
                        foreach ($propertyValue as $item) {
                            $values[] = $this->newInstance($property['type'], $item, $fullProperties);
                        }
                        $this->setPropertyValue($propertyName, $values);
                    }
                } elseif ($propertyValue || !$property['allows_null']) {
                    //如果解析的文档对象是个独立对象, 并是有值或者不能为空的, 直接实例化文档对象并设置值
                    $value = $this->newInstance($property['type'], $propertyValue, $fullProperties);
                    $this->setPropertyValue($propertyName, $value);
                }
            } catch (\TypeError $e) {
                //抛出类型错误消息
                preg_match("/must be \w+/", $e->getMessage(), $match);
                //如果是当前setPropertyValue设置的, 则说明$propertyName已经取得, 否则可能是其他地方报的类型错误(比如BeforeFill里面)
                $typeError = $fullPropertyName . ' ' . ($match[0] ?? '');
                throw new ErrorFieldException("Type Error: {$typeError}", $fullPropertyName);
            } catch (\ErrorException $e) {
                if ($e->getMessage() === 'A non well formed numeric value encountered') {
                    throw new ErrorFieldException("Type Error: {$fullPropertyName} must be number", $fullPropertyName);
                }
                throw $e;
            }
        }
    }


    /**
     * 设置属性值
     *
     * @param string $property
     * @param $value
     */
    private function setPropertyValue(string $property, $value): void
    {
        $this->class->{$property} = $value;
    }

    /**
     * 实例化对象
     * @param string $className
     * @param $params
     * @return mixed
     */
    private function newInstance(string $className, $params, array $parentProperties = [])
    {
        return new $className($params, $parentProperties);
    }



    /**
     * 获取对象文档属性类型
     *
     * @return array
     * @throws \ReflectionException
     */
    public function getPropertyTypes(): array
    {
        $classPropertyTypes = &self::$propertyTypes[$this->className];
        if (!isset($classPropertyTypes)) {
            $classPropertyTypes = [];
            $reflection = new \ReflectionClass($this->class);
            $properties = $reflection->getProperties();
            foreach ($properties as $property) {
                if ($property->isPublic()) {
                    $name = $property->getName();
                    $classPropertyTypes[$name] = [
                        'type' => 'string',
                        'is_class' => false,
                        'is_collection' => false,
                        'allows_null' => true,
                        'transformer' => null,//转换器, 用于转换为新的值
                    ];

                    //根据参数Doc文档设置指定对象值
                    $doc = $property->getDocComment();


                    //如果属性是强类型, 则以强类型为主将类型注入到propertyTypes里
                    if ($property->hasType() && $property->getType() instanceof \ReflectionNamedType) {
                        $typeName = $property->getType()->getName();
                        $isArray = $typeName === 'array';
                        $classPropertyTypes[$name] = [
                                'type' => $typeName,
                                'is_class' => $this->isClassType($typeName),
                                'is_collection' => $isArray,
                                'allows_null' => $property->getType()->allowsNull(),
                            ] + $classPropertyTypes[$name];

                        //如果不是数组的话则已经确定类型了, 不必往下走
                        if (!$isArray) {
                            continue;
                        }
                    }


                    if ($doc) {
                        //解析类似@var \ApiService_Developer_RequestBean_DemoListBean[]的文档对象
                        preg_match("/@var \??(.*?)(\[\])?[\|\s\*]/", $doc, $matches);

                        //解析属性类型到propertyTypes
                        //如果未设置强类型, 或者强类型为数组并且注释类型为对象数组时则重新设置类型
                        $matchType = $matches[1] ?? '';
                        //如果注释没有命名空间, 则查看是否再当前命名空间下存在该类
                        if (!class_exists($matchType)) {
                            $classType = $reflection->getNamespaceName() . "\\" . $matchType;
                            if (class_exists($classType)) {
                                $matchType = $classType;
                            }
                        }

                        $isCollection = isset($matches[2]);

                        if (($matchType && !isset($classPropertyTypes[$name]['type'])) || ($isCollection && $classPropertyTypes[$name]['type'] === 'array' && class_exists($matchType))) {
                            $classPropertyTypes[$name]['type'] = $matchType;
                            $classPropertyTypes[$name]['is_class'] = $this->isClassType($matchType);
                        }

                        if (!isset($classPropertyTypes[$name]['is_collection'])) {
                            $classPropertyTypes[$name]['is_collection'] = $isCollection;
                        }

                        if (!isset($classPropertyTypes[$name]['allows_null'])) {
                            $classPropertyTypes[$name]['allows_null'] = true;
                        }
                    }
                }
            }
        }
        return self::$propertyTypes[$this->className];
    }

    /**
     * 是否是类
     *
     * @param string $type
     * @return bool
     */
    protected function isClassType(string $type): bool
    {
        return class_exists($type);
    }
}