<?php
namespace Central;

class Payload
{
    protected $_interface;

    protected $_expiry;

    const VERSION = '0.3.0';

    public function __construct($interface, $expiry = null)
    {
        $this->_interface = $interface;
        $this->_expiry = $expiry;
    }

    public function toArray()
    {
        return array(
            'Id'        => uniqid(),
            'Version'   => self::VERSION,
            'Name'      => $this->_interface->getName(),
            'Interface' => php_sapi_name() === 'cli' ? 'CLI' : 'WEB',
            'Script'    => basename($this->_interface()->getArguments()[0]),
            'Arguments' => implode(' ', array_slice($this->_interface()->getArguments(), 1)),
            'Timestamp' => date('Y-m-d H:i:s', $this->_interface->getTimestamp()),
            'Duration'  => $this->_interface->getDuration(),
            'Memory'    => $this->_interface->getPeakMemoryUsed(),
            'Host'      => gethostname(),
            'Status'    => $this->_interface->getExitStatus(),
            'Message'   => $this->_interface->getExitMessage(),
            'Expires'   => $this->_expiry,
            'Errors'    => count($this->_interface->log()->getErrors()),
            'Entries'   => $this->_interface->log()->toArray(),
        );
    }
}
