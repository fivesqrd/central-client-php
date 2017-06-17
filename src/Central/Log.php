<?php
namespace Central;

class Log
{
    protected $_entries = array();

    public function error($string)
    {
        array_push($this->_entries, array(
            'Timestamp' => date('Y-m-d H:i:s'),
            'Message'   => $string,
            'Type'      => 'error'
        ));
    }

    /**
     * Append string to the last entry and override type
     */
    public function append($message, $type = 'debug')
    {
        $index = count($this->_entries) - 1;

        /* get last entry is */
        $current = $this->_entries[$index];

        /* Append to the message and override the previously set entry type */
        $this->_entries[$index] = array_merge($this->_entries[$index], array(
            'Message' => $current['Message'] . '... ' . $message,
            'Type'    => $type
        ));
    }

    public function debug($string)
    {
        array_push($this->_entries, array(
            'Timestamp' => date('Y-m-d H:i:s'),
            'Message'   => $string,
            'Type'      => 'debug'
        ));
    }

    public function toArray()
    {
        return $this->_entries;
    }
}
