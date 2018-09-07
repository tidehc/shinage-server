<?php
declare(strict_types=1);

/*
 * Copyright 2018 by Michael Zapf.
 * Licensed under MIT. See file /LICENSE.
 */

namespace AppBundle\Presentation\Slideshow;

use AppBundle\Entity\Presentation;
use AppBundle\Presentation\PresentationRendererInterface;
use AppBundle\Presentation\SettingsReaderInterface;

class Renderer implements PresentationRendererInterface
{
    /** @var SettingsReaderInterface */
    private $settingsReader;

    public function __construct(
        SettingsReaderInterface $settingsReader
    ) {
        $this->settingsReader = $settingsReader;
    }

    public function render(Presentation $presentation): string
    {
        /** @var Settings $parsedSettings */
        $parsedSettings = $this->settingsReader->get($presentation->getSettings());
        $count = count($parsedSettings->getSlides());
        return "
<!doctype html>
<html>
    <head>
      <meta charset='utf-8'>
      <meta http-equiv='x-ua-compatible' content='ie=edge'>
      <meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=no'>
      <title></title>
      <style>
        ::-webkit-scrollbar {
            display: none;
        }
        
        html, body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            height: 100%;
        }
        
        body {
            max-height: 100%;
            float: left;
            width: 100%;
        }
        
        #container {
            display: block;
            margin: 0;
            padding: 0;
            width: 100%;
            min-width: 100%;
            max-width: 100%;
            height: 100%;
            min-height: 100%;
            max-height: 100%;
            overflow: hidden;
        }
      
        /* TODO */
      </style>
    </head>
    <body>
        <div id='container'>TODO Reveal.js</div>
        <!-- Slide count: $count -->
    </body>
</html>
        ";
    }
}