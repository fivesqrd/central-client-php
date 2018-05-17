<?php
namespace Fivesqrd\Central;

class Profile
{
    protected $_adapter;

    public function __construct($adapter, $ratio = null)
    {
        $this->_adapter = $adapter;
        $this->_adapter->setEnabled($this->_getRandomFlag($ratio));
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
                'Query'     => $profile->getQuery(),
                'Params'    => $profile->getQueryParams(),
                'Duration'  => $profile->getElapsedSecs()
            ));
        }

        return $queries;
    }
}
