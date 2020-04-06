<?php

use \Joomla\CMS\Factory;

class IsDetail
{
    private $item;
    private $match;
    private $key;

    public function __construct($item)
    {
        $this->item = $item;
    }

    public function match($value)
    {
        $item = array_filter($this->item, function ($item) use ($value) {
            return $item->alias === $value || (int)$item->id === (int)$value;
        });
        sort($item);
        $this->match = array_pop($item);
    }

    public function setKey()
    {
        $config = Factory::getConfig();
        $isSef = $config->get('sef');
        $this->key = $isSef ? 'alias' : 'id';
    }

    public function get()
    {
        return $this->match;
    }

}