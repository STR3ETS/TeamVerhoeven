<?php

namespace App\Policies;

use App\Models\Thread;
use App\Models\User;

class ThreadPolicy
{
    /**
     * Mag de gebruiker deze thread zien?
     * - Coach: eigen user-id moet matchen met threads.coach_user_id
     * - Client: eigen user-id moet matchen met threads.client_user_id
     * - Admin: altijd
     */
    public function view(User $user, Thread $thread): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCoach()) {
            return (int) $thread->coach_user_id === (int) $user->id;
        }

        if ($user->isClient()) {
            return (int) $thread->client_user_id === (int) $user->id;
        }

        return false;
    }

    /**
     * Mag de gebruiker een thread aanmaken?
     */
    public function create(User $user): bool
    {
        return $user->isClient() || $user->isCoach() || $user->isAdmin();
    }

    /**
     * Mag de gebruiker reageren in deze thread?
     * Zelfde check als view.
     */
    public function reply(User $user, Thread $thread): bool
    {
        return $this->view($user, $thread);
    }

    /**
     * Mag de gebruiker deze thread verwijderen / sluiten?
     * - Admin: altijd
     * - Coach: alleen als hij/zij aan deze thread gekoppeld is
     * - Client: nooit
     */
    public function delete(User $user, Thread $thread): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCoach()) {
            return (int) $thread->coach_user_id === (int) $user->id;
        }

        return false;
    }
}
