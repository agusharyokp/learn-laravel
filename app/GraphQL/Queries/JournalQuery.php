<?php
namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class JournalQuery
{
    /**
     * Return a list of journals for the authenticated user.
     *
     * @param  null  $_
     * @param  array{}  $args
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function myJournals($_, array $args)
    {
        // Handle authentication manually
        $user = auth()->user();
        if (!$user) {
            throw new \Exception('Unauthorized');
        }

        return $user->journals()->latest()->get();
    }
}
