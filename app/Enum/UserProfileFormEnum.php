<?php

namespace App\Enum;

enum UserProfileFormEnum: string
{
    case Name ='name';
    case Email = 'email';
    case Password = 'password';
}
