<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once 'AbstractModel.php';

class Task extends AbstractModel
{
    protected $mainTable = 'task';

    protected $idFieldName = 'task_id';

    /**
     * Retrieve short description
     *
     * @return false|string
     */
    public function getShortDescription()
    {
        return substr($this->getData('description') ?? '', 0, 40) . '...';
    }

    /**
     * Retrieve task status
     *
     * @return string
     */
    public function getStatus()
    {
        switch ($this->getData('is_complete')) {
            case '1':
                return lang('complete');
            default:
                return lang('incomplete');
        }
    }
}
