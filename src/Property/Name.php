<?php

namespace Report\Property;


use Report\Renderer\AbstractRenderer;

trait Name
{
    /**
     * @var string|null
     */
    protected $name = null;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return $this
     */
    public function setName(?string $name)
    {
        $scope = AbstractRenderer::getScope();

        if ($this->name !== null) {
            $scope->deleteVariable($this->name);
        }

        $this->name = $name;

        if ($this->name !== null) {
            $scope->registerVariable($this->name, $this);
        }

        return $this;
    }

}
