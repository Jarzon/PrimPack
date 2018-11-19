<?php
namespace PrimPack\Service;

class Admin
{
    protected $isAdmin = false;

    public function __construct(bool $isAdmin = false) {
        $this->isAdmin = $isAdmin;
    }

    function isAdmin(): bool
    {
        return $this->isAdmin;
    }
}