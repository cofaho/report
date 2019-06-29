<?php

namespace Report\Data\DataSet;


use PDO;
use PDOStatement;

class PDODataSet extends AbstractDataSet
{
    /**
     * @var PDOStatement
     */
    protected $statement;

    public function __construct(PDOStatement $statement = null, string $name = null)
    {
        if ($statement) {
            $this->setStatement($statement);
        }
        parent::__construct($name);
    }

    /**
     * @param PDOStatement $statement
     * @return $this
     */
    public function setStatement(PDOStatement $statement)
    {
        $this->statement = $statement;
        return $this;
    }

    /**
     * @return PDOStatement
     */
    public function getStatement(): PDOStatement
    {
        return $this->statement;
    }

    public function rewind()
    {
        if ($this->isActive) {
            $this->statement->closeCursor();
        }
        $this->statement->execute();
        $this->data = $this->statement->fetch(PDO::FETCH_ASSOC);
        if ($this->data === false) {
            $this->data = null;
        }
        parent::rewind();
    }

    public function open()
    {
        if ($this->masterLink !== null) {
            if (is_array($this->masterLink)) {
                foreach ($this->masterLink as $link) {
                    $this->statement->bindValue($link->getDetailField(), $link->getMasterFieldValue());
                }
            } else {
                $this->statement->bindValue($this->masterLink->getDetailField(), $this->masterLink->getMasterFieldValue());
            }
        }
        parent::open();
    }

    public function close()
    {
        if ($this->isActive) {
            $this->statement->closeCursor();
        }
        parent::close();
    }

    public function next()
    {
        $this->data = $this->statement->fetch(PDO::FETCH_ASSOC);
        if ($this->data === false) {
            $this->data = null;
        }
        parent::next();
    }

    public function prev()
    {
        $this->data = $this->statement->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_PRIOR);
        parent::prev();
    }

}
