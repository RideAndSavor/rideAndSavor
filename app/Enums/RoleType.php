<?php

namespace App\Enums;

enum RoleType: string
{
  case Admin = 'admin';
  case Rider = 'rider';
  case Driver = 'driver';
  case Owner = 'owner';
}
