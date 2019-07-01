<?php

namespace Report\Band\Property;


interface GroupInterface
{
    /**
     * @return string
     */
    public function getGroupFieldName(): string;

    /**
     * @param string $groupFieldName
     * @return $this
     */
    public function setGroupFieldName(string $groupFieldName);

    /**
     * @param string|null $groupValue
     * @return mixed
     */
    public function setGroupValue(?string $groupValue);
}
