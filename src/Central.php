<?php

class Central
{
    public static $options = array();

    public static function job($name, $args)
    {
        $object = new Central\Job($name, $args);

        if (isset(self::$options['aws'])) {
            $object->setStorage(
                new Central\Storage\DynamoDb(self::$options)
            );
        }

        return $object;
    }
}
