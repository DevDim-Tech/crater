<?php

namespace Crater\Policies;

use Crater\Models\Budget;
use Crater\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Silber\Bouncer\BouncerFacade;

class BudgetPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \Crater\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        if (BouncerFacade::can('view-budget', Budget::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Budget  $budget
     * @return mixed
     */
    public function view(User $user, Budget $budget)
    {
        if (BouncerFacade::can('view-budget', $budget) && $user->hasCompany($budget->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \Crater\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if (BouncerFacade::can('create-budget', Budget::class)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Budget  $budget
     * @return mixed
     */
    public function update(User $user, Budget $budget)
    {
        if (BouncerFacade::can('edit-budget', $budget) && $user->hasCompany($budget->company_id)) {
            // Budgets can be edited unless they are converted to invoices
            return $budget->status !== Budget::STATUS_CONVERTED;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Budget  $budget
     * @return mixed
     */
    public function delete(User $user, Budget $budget)
    {
        if (BouncerFacade::can('delete-budget', $budget) && $user->hasCompany($budget->company_id)) {
            // Budgets cannot be deleted if they are converted to invoices
            return $budget->status !== Budget::STATUS_CONVERTED;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Budget  $budget
     * @return mixed
     */
    public function restore(User $user, Budget $budget)
    {
        if (BouncerFacade::can('delete-budget', $budget) && $user->hasCompany($budget->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Budget  $budget
     * @return mixed
     */
    public function forceDelete(User $user, Budget $budget)
    {
        if (BouncerFacade::can('delete-budget', $budget) && $user->hasCompany($budget->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can send budgets.
     *
     * @param  \Crater\Models\User  $user
     * @param  \Crater\Models\Budget  $budget
     * @return mixed
     */
    public function send(User $user, Budget $budget)
    {
        if (BouncerFacade::can('send-budget', $budget) && $user->hasCompany($budget->company_id)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete multiple budgets.
     *
     * @param  \Crater\Models\User  $user
     * @return mixed
     */
    public function deleteMultiple(User $user)
    {
        if (BouncerFacade::can('delete-budget', Budget::class)) {
            return true;
        }

        return false;
    }
}
