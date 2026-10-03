<?php

namespace App\Enum;

/**
 * Forms of the profile, example 3. The value is the option of the rule "example3.update".
 */
enum UserProfileFormEnum: string
{
    case Name = 'name';
    case Email = 'email';
    case Password = 'password';
}
