<?php


namespace FillData\Test\Example;


class UserBean extends \FillData\ToObject
{
    /** @var string  */
    public string $name;

    /** @var string  */
    public string $age;

    /** @var string  */
    public string $sex;

    /** @var \FillData\Test\Example\Job $job  */
    public Job $job;
}