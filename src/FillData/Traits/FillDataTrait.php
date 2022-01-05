<?php


namespace FillData\Traits;


use FillData\PropertyDocParser;

trait FillDataTrait
{
    /** @var bool true时会自动将不含数字key的一位数组转为多维数组(如果属性是多维对象数组的话), 多用于XML解析时单个和多个元素解析的结构不同 */
    protected bool $isSmartFill = false;


    //将数组配置注入实体对象, 设置的父属性列表, 用于知道当前Field属于哪个父属性
    public function __construct($data = null, array $parentProperties = [])
    {
        $data && $this->fill($data, $parentProperties);
    }

    /**
     * 将数组配置注入实体对象
     * @param $data
     * @param array $parentProperties
     * @return $this
     * @throws \ReflectionException
     */
    public function fill($data, array $parentProperties = [])
    {
        $this->fillTypeData($data, $parentProperties);

        return $this;
    }


    /**
     * 填充数据
     * @param $data
     * @throws \ReflectionException
     */
    protected function fillTypeData($data, array $parentProperties = []): void
    {
        if (is_array($data) || is_object($data)) {
            $this->beforeFill($data);
            $parser = new PropertyDocParser($this);
            $parser->fillTypeData($data, $this->isSmartFill, $parentProperties);
            $this->afterFill();
        }
    }

    /**
     * 填充前操作
     */
    protected function beforeFill(&$data): void
    {

    }

    /**
     * 填充后操作
     */
    protected function afterFill(): void
    {

    }

    /**
     * 将对象数组转为当前对象数组
     *
     * @param $data
     * @return $this|mixed
     */
    public static function fromItem($data): self
    {
        return new static($data);
    }

    /**
     * 将多维数组转为当前对象数组
     *
     * @param array $list
     * @return array
     */
    public static function fromList(array $list): array
    {
        $data = [];
        foreach ($list as $item) {
            $data[] = new static($item);
        }
        return $data;
    }
}