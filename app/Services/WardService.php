<?php
namespace App\Services;

use App\Contracts\WardInterface;

class WardService 
{
    protected $wardInterface;

    public function __construct(WardInterface $wardInterface)
    {
        $this->wardInterface = $wardInterface;
    }
    
    public function getWardsByTownship($township_id)
    {
        return $this->wardInterface->getWardsByTownship($township_id);
    }
}