<?php
namespace Central;

class Profile
{
    protected $_profiler;

    public function __construct($profiler, $ratio = null)
    {
        $this->_profiler = $profiler;
        $profiler->setEnabled($this->_getRandomFlag($ratio));
    }

    protected function _getRandomFlag($ratio)
    {
        if ($ratio === null) {
            return true;
        }

        return (mt_rand(1, $ratio) == 1);
    }

    public function getQueries()
    {
        $profiles = $this->_adapter->getQueryProfiles();

        if (!is_array($profiles)) {
            return array();
        }

        return $profiles;
    }

    public function toArray()
    {
        $queries = array();

        foreach ($this->getQueries() as $profile) {
            array_push($queries, array(
                'query'     => $profile->getQuery(),
                'params'    => $profile->getQueryParams(),
                'duration'  => $profile->getElapsedSecs()
            ));
        }

        return $queries;
    }
}
