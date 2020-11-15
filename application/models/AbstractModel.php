<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class AbstractModel extends PS_Object
{
    /**
     * @var string
     */
    protected $mainTable;

    /**
     * @var string
     */
    protected $idFieldName;

    /**
     * @var string
     */
    protected $tableAlias = 'main_table';

    /**
     * Retrieve main table name
     *
     * @return string
     * @throws Exception
     */
    public function getMainTable()
    {
        if (is_null($this->mainTable)) {
            throw new Exception('Table name is not specified');
        }

        return $this->mainTable;
    }

    /**
     * Get model id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->getData($this->idFieldName);
    }

    /**
     * Load data
     *
     * @param $field
     * @param bool $value
     * @return AbstractModel
     */
    public function load($field, $value = false)
    {
        if (false === $value) {
            $value = $field;
            $field = $this->idFieldName;
        }

        try {
            $collection = $this->getCollection();
        } catch (Exception $e) {
            log_message('exception', $e->getMessage());
            return $this;
        }

        $collection->addFieldToFilter($field, $value);
        return $collection->getFirstItem();
    }

    /**
     * @return $this
     */
    protected function beforeSave()
    {
        return $this;
    }

    /**
     * Save model
     *
     * @return $this
     * @throws Exception
     */
    public function save()
    {
        $this->beforeSave();

        if (is_null($this->getIdFieldName())) {
            throw new Exception('Id field name is not specified');
        }

        if ($this->getId()) {
            $this->update();
        } else {
            $this->create();
        }

        return $this;
    }

    /**
     * Remove entity
     *
     * @return $this
     * @throws Exception
     */
    public function delete()
    {
        if (is_null($this->getIdFieldName())) {
            throw new Exception('Id field name is not specified');
        }

        if ($this->getId()) {
            /** @var CI_DB_query_builder $db */
            $db = $this->db;
            $db->trans_start();
            $db->delete($this->mainTable, [$this->idFieldName => $this->getId()]);
            $db->trans_complete();
        }

        return $this;
    }

    /**
     * Create db record
     *
     * @return $this
     */
    public function create()
    {
        /** @var CI_DB_query_builder $db */
        $db = $this->db;
        $db->trans_start();
        $db->insert($this->mainTable, $this->getData());
        $lastId = $db->insert_id();
        $db->trans_complete();

        $this->addData([$this->idFieldName => $lastId]);
        return $this;
    }

    /**
     * @return $this
     */
    public function update()
    {
        /** @var CI_DB_query_builder $db */
        $db = $this->db;
        $db->trans_start();
        $db->update($this->mainTable, $this->getData(), [$this->idFieldName => $this->getId()]);
        $db->trans_complete();

        return $this;
    }

    /**
     * @return PS_Collection
     * @throws Exception
     */
    public function getCollection()
    {
        $db = clone $this->db;
        $collection = new PS_Collection($db, $this->getMainTable(), get_class($this));
        return $collection;
    }

    /**
     * @return string
     */
    public function getIdFieldName()
    {
        return $this->idFieldName;
    }
}
