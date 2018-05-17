<?php
namespace Fivesqrd\Central;

class Payload
{
    protected $_interface;

    protected $_profile;

    protected $_expiry;

    protected $_log;

    const VERSION = '1.0.0';

    public function __construct($spec, $expiry = null)
    {
        $this->_expiry    = $expiry;
        $this->_interface = $spec['interface'];
        $this->_log       = $spec['log'];

        if (isset($spec['profile'])) {
            $this->_profile = $spec['profile'];
        }
    }

    public function toArray()
    {
        return array(
            'Id'        => uniqid(),
            'Version'   => self::VERSION,
            'Name'      => $this->_interface->getName(),
            'Interface' => php_sapi_name() === 'cli' ? 'CLI' : 'WEB',
            'Script'    => $this->_interface->getName(),
            'Arguments' => implode(' ', $this->_interface->getArguments()),
            'Timestamp' => date('Y-m-d H:i:s', $this->_interface->getTimestamp()),
            'Duration'  => $this->_interface->getDuration(),
            'Memory'    => $this->_interface->getPeakMemoryUsed(),
            'Host'      => gethostname(),
            'Status'    => $this->_interface->getExitStatus(),
            'Message'   => $this->_interface->getExitMessage(),
            'Expires'   => $this->_expiry,
            'Errors'    => count($this->_log->getErrors()),
            'Entries'   => $this->_log->toArray(),
            'Queries'   => $this->_profile ? $this->_profile->toArray() : array()
        );
    }

    public function attributes($namespace)
    {
        if (!$namespace) {
            throw new Exception('Namespace not provided in configuration');
        }

        $attributes = array(
            'Namespace' => (string) $namespace
        );

        foreach ($this->toArray() as $key => $value) {
            
            if ($value === null || $value == '') {
                /* empty strings are not allowed in DynamoDb */
                continue;
            }

            $attributes[$key] = $value;
        }

        return $attributes;
    }
}
