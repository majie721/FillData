<?php


namespace FillData\Traits;


trait PropertyToArrayTrait
{
    /** @var array 当前属性集合 */
    protected static array $_properties = [];

    /**
     * 转化为数组形式, 未设置的值会忽略
     * @return array
     * @throws \JsonException
     */
    public function toArray(): array
    {
        return (array)json_decode(json_encode($this, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
    }


    /**
     * @throws \JsonException
     */
    public function toJson(): string
    {
        return json_encode($this, JSON_THROW_ON_ERROR);
    }


    /**
     * 获取属性集合
     *
     * @return array
     */
    public static function getProperties(): array
    {
        $className = static::class;
        if (! isset(static::$_properties[$className]['name'])) {
            $info = static::getPropertyInfo();
            $properties = array_column($info, 'name');
            static::$_properties[$className]['name'] = $properties;
        }
        return static::$_properties[$className]['name'];
    }


    /**
     * 获取属性集合
     *
     * @return array
     */
    public static function getPropertyInfo(): array
    {
        $className = static::class;
        if (! isset(static::$_properties[$className]['info'])) {
            $reflection = new \ReflectionClass(static::class);
            $properties = $reflection->getProperties();
            foreach ($properties as $property) {
                if ($property->isPublic()) {
                    static::$_properties[$className]['info'][] = [
                        'name' => $property->getName(),
                        'type' => $property->hasType() ? $property->getType()->getName() : null,
                        'nullable' => !$property->hasType() || $property->getType()->allowsNull(),
                    ];
                }
            }
        }
        return static::$_properties[$className]['info'];
    }
}