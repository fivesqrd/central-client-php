<?php
namespace Central;

class Log
{
    protected $_entries = array();

    public function error($value)
    {
        if ($value instanceof \Exception) {
            array_push($this->_entries, $this->_getExceptionEntry($value));
            return;
        }

        array_push($this->_entries, array(
            'Timestamp' => date('Y-m-d H:i:s'),
            'Message'   => $value,
            'Type'      => 'error'
        ));
    }

    protected function _getExceptionEntry($e)
    {
        return array(
            'Timestamp' => date('Y-m-d H:i:s'),
            'Message'   => $e->getMessage(),
            'Type'      => 'error',
            'Trace'     => $e->getTraceAsString()
        );
    }

    /**
     * Append string to the last entry and override type
     */
    public function append($value, $type = null)
    {
        $index = count($this->_entries) - 1;

        if ($index < 0) {
            return;
        }

        /* get last entry is */
        $current = &$this->_entries[$index];

        /* Append to the message and override the previously set entry type */
        if ($type !== null && $type != $current['Type']) {
            $current['Type'] = $type;
        }

        /* Initiate the array if it doesn't exit */
        if (!array_key_exists('Extra', $current)) {
            $current['Extra'] = array();
        }

        /* If we have exception data, we'll update the entry accordingly */
        if ($value instanceof \Exception) {
            $current['Trace'] = $value->getTraceAsString();
            $current['Type'] = 'error';
            array_push($current['Extra'], $value->getMessage());
        } else {
            array_push($current['Extra'], $value);
        }

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
