<?php
namespace App\GraphQL\Mutations;

use App\Models\Journal;
use App\Services\RabbitMQPublisher;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class JournalMutation
{
    public function createJournal($rootValue, array $args, GraphQLContext $context)
    {
        $journal = Journal::createForUser($context->user(), $args);
        app(RabbitMQPublisher::class)->publishModelEvent($journal, 'created', 'journal', 'journal.created');
        
        return $journal;
    }

    public function updateJournal($rootValue, array $args, GraphQLContext $context)
    {
        $journal = $context->user()->journals()->findOrFail($args['id']);
        return $journal->updateWithData($args);
    }
}

