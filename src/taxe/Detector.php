<?php

namespace App\taxe;

class Detector
{
    private $seuil;
    public function __construct($seuil)
    {
        $this->seuil = $seuil;
    }

    public function detect(int $price) : bool
   
    {
        return $price > $this->seuil ? true : false;
    }
   
}