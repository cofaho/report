<?php

namespace Report\Band\Property;


trait Group
{
    /**
     * @var string
     */
    protected $groupFieldName;
    /**
     * @var string
     */
    protected $groupValue;

    /**
     * @return string
     */
    public function getGroupFieldName(): string
    {
        return $this->groupFieldName;
    }

    /**
     * @param string $groupFieldName
     * @return $this
     */
    public function setGroupFieldName(string $groupFieldName)
    {
        $this->groupFieldName = $groupFieldName;
        return $this;
    }

    /**
     * @param string|null $groupValue
     * @return $this
     */
    public function setGroupValue(?string $groupValue)
    {
        $this->groupValue = $groupValue;
        return $this;
    }

}
