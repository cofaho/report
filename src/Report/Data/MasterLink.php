<?php


namespace Report\Data;


use Report\Data\DataSet\DataSetInterface;

class MasterLink
{
    /**
     * @var DataSetInterface|null
     */
    protected $masterDataSet;
    /**
     * @var string|string[]
     */
    protected $masterField;
    /**
     * @var string|string[]
     */
    protected $detailField;

    public function __construct($detailField = null, $masterDataSet = null, $masterField = null)
    {
        $this->masterDataSet = $masterDataSet;
        $this->masterField = $masterField;
        $this->detailField = $detailField;
    }

    /**
     * @param null|string $detailField
     * @return $this
     */
    public function setDetailField($detailField)
    {
        $this->detailField = $detailField;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getDetailField()
    {
        return $this->detailField;
    }

    /**
     * @param DataSetInterface|null $masterDataSet
     * @return $this
     */
    public function setMasterDataSet($masterDataSet)
    {
        $this->masterDataSet = $masterDataSet;
        return $this;
    }

    /**
     * @return DataSetInterface|null
     */
    public function getMasterDataSet()
    {
        return $this->masterDataSet;
    }

    /**
     * @param null|string $masterField
     * @return $this
     */
    public function setMasterField($masterField)
    {
        $this->masterField = $masterField;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getMasterField()
    {
        return $this->masterField;
    }

    /**
     * @return mixed
     */
    public function getMasterFieldValue()
    {
        return $this->masterDataSet->field($this->masterField);
    }

}
