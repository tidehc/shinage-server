<?php
declare(strict_types=1);

/*
 * Copyright 2018 by Michael Zapf.
 * Licensed under MIT. See file /LICENSE.
 */

namespace AppBundle\Entity\Slides;

use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\ExclusionPolicy("NONE")
 */
class ImageSlide extends Slide
{
    /**
     * @var string
     *
     * @JMS\Type("string")
     */
    protected $src = '';

    /**
     * @var string
     *
     * @JMS\Type("string")
     */
    protected $type = 'Image';

    public function getSrc(): string
    {
        return $this->src;
    }

    public function setSrc(string $src): self
    {
        $this->src = $src;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Slide
    {
        $this->type = $type;
        return $this;
    }
}
