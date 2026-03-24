<?php

namespace App;

trait HasPermissions
{
    public function hasPermission($permission)
    {
        return $this->permissions->contains('name', $permission);
    }
}
