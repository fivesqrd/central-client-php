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
    public function append($message, $type = null)
    {
        $index = count($this->_entries) - 1;

        /* get last entry is */
        $current = &$this->_entries[$index];

        /* Append to the message and override the previously set entry type */
        if ($type !== null && $type != $current['Type']) {
            $current['Type'] = $type;
        }

        if (!array_key_exists('Extra', $current)) {
            $current['Extra'] = array();
        }

        array_push($current['Extra'], $message);
    }

    public function debug($string)
    {
        array_push($this->_entries, array(
            'Timestamp' => date('Y-m-d H:i:s'),
            'Message'   => $string,
            'Type'      => 'debug'
        ));
    }

    public function getErrors()
    {
        return array_filter($this->_entries, function ($entry) {
            return ($entry['Type'] == 'error');
        });
    }

    public function toArray()
    {
        return $this->_entries;
    }
}
