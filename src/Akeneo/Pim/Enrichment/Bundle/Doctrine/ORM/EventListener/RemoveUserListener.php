<?php

declare(strict_types=1);

namespace Akeneo\Pim\Enrichment\Bundle\Doctrine\ORM\EventListener;

use Akeneo\Pim\Enrichment\Component\Comment\Model\Comment;
use Akeneo\UserManagement\Component\Model\UserInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2019 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @todo merge 3.1: Remove this listener, we should instead add an ON DELETE SET NULL on the author foreign key constraint
 */
class RemoveUserListener
{
    /**
     * Removes the author of comments and replies when the matching user is deleted
     *
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args): void
    {
        $user = $args->getObject();
        if (!$user instanceof UserInterface) {
            return;
        }

        $commentsToUpdate = $args->getObjectManager()->getRepository(Comment::class)->findBy(['author' => $user]);
        foreach ($commentsToUpdate as $comment) {
            $comment->setAuthor(null);
            $args->getObjectManager()->persist($comment);
        }
    }
}
