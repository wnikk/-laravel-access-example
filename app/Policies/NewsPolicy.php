<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NewsPolicy
{
    public function viewThisIfThisIsTest(User $user, News $news): ?bool
    {
        if(
            $user->can('Example7News.test')
            && stripos($user->name, 'test') !== false
            && stripos($news->description, 'test') !== false
        ) {
            return true;
        }
        return null;
    }
}
