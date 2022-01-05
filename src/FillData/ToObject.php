<?php


namespace FillData;



use FillData\Interfaces\ArrayAble;
use FillData\Traits\ArrayAccessTrait;
use FillData\Traits\PropertyToArrayTrait;
use FillData\Traits\FillDataTrait;

abstract class ToObject implements \ArrayAccess,ArrayAble
{
    use ArrayAccessTrait,PropertyToArrayTrait,FillDataTrait;
}