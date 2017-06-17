<?php
namespace Central;

class Log
{
    protected $_lines = array();

    public function error($string)
    {
        array_push($this->_lines, array(
            'Timestamp' => date('Y-m-d H:i:s'),
            'Message'   => $string,
            'Type'      => 'error'
        ));
    }

    public function debug($string)
    {
        array_push($this->_lines, array(
            'Timestamp' => date('Y-m-d H:i:s'),
            'Message'   => $string,
            'Type'      => 'debug'
        ));
    }

    public function toArray()
    {
        return $this->_lines;
    }
}
