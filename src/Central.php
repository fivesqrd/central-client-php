<?php

class Central
{
    public static $options = array();

    public static function job($name, $args)
    {
        return new Central\Job(
            $name, $args, new Central\Log()
        );
    }

    public static function save($spec, $expiry = null)
    {
        if (isset(self::$options['aws'])) {
            throw new Exception(
              "Save operation is not possible if no storage config is provided"
            );
        }

        $storage = new Central\Storage\DynamoDb(self::$options);

        $storage->add(new Central\Payload($spec, $expiry));
    }
}
