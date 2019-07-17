<?php
namespace PrimPack\Service;

class Admin
{
    protected $isAdmin = false;

    public function __construct(array $session) {
        $this->isAdmin = $session['isAdmin']?? false;
    }

    function isAdmin(): bool
    {
        return $this->isAdmin;
    }
}
