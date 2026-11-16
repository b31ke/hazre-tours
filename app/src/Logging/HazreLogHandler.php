<?php

namespace HazreTours\Logging;

use Monolog\Handler\StreamHandler;
use Psr\Log\LogLevel;

/**
 * Greške na serveru idu u ~/hazre-tours/hazre-greske.log (izvan public/).
 * Putanja se računa ovdje jer YAML ne zna spojiti BASE_PATH s ostatkom putanje.
 */
class HazreLogHandler extends StreamHandler
{
    public function __construct()
    {
        parent::__construct(BASE_PATH . '/hazre-greske.log', LogLevel::WARNING);
    }
}
