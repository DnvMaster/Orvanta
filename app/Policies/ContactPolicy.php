<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;

class ContactPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('contacts.view');
    }

    public function view(User $user, Contact $contact): bool
    {
        return $this->hasAccessToContact($user, $contact, 'contacts.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('contacts.create');
    }

    public function update(User $user, Contact $contact): bool
    {
        return $this->hasAccessToContact($user, $contact, 'contacts.update');
    }

    public function delete(User $user, Contact $contact): bool
    {
        return $this->hasAccessToContact($user, $contact, 'contacts.delete');
    }

    public function restore(User $user, Contact $contact): bool
    {
        return false;
    }

    public function forceDelete(User $user, Contact $contact): bool
    {
        return false;
    }

    private function hasAccessToContact(User $user, Contact $contact, string $permission): bool
    {
        $scope = $user->getPermissionScope($permission);
        if ($scope === null) {
            return false;
        }
        $hasContactAccess = match ($scope) {
            'all' => true,
            'own' => $contact->user_id !== null && (int) $contact->user_id === (int) $user->getKey(),
            'department' => $this->belongsToAccessibleDepartment($user, $contact),
            default => false
        };
        if (! $hasContactAccess) {
            return false;
        }
        if ($contact->company_id !== null) {
            $company = $contact->company;
            if ($company === null || ! $user->can('view', $company)) {
                return false;
            }
        }

        return true;
    }

    private function belongsToAccessibleDepartment(User $user, Contact $contact): bool
    {
        $owner = $contact->responsible;
        if ($owner === null || $owner->department_id === null) {
            return false;
        }

        return in_array((int) $owner->department_id, $user->accessibleDepartmentIds(), true);
    }
}
