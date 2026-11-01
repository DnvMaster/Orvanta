<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;

class ContactPolicy
{
    /**
     * Просмотр списка контактов.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('contacts.view');
    }

    /**
     * Просмотр конкретного контакта.
     */
    public function view(User $user, Contact $contact): bool
    {
        return $this->hasAccessToContact($user, $contact, 'contacts.view');
    }

    /**
     * Создание контакта.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('contacts.create');
    }

    /**
     * Изменение контакта.
     */
    public function update(User $user, Contact $contact): bool
    {
        return $this->hasAccessToContact($user, $contact, 'contacts.update');
    }

    /**
     * Удаление контакта.
     */
    public function delete(User $user, Contact $contact): bool
    {
        return $this->hasAccessToContact($user, $contact, 'contacts.delete');
    }

    /**
     * Восстановление контакта.
     */
    public function restore(User $user, Contact $contact): bool
    {
        return false;
    }

    /**
     * Полное удаление контакта.
     */
    public function forceDelete(User $user, Contact $contact): bool
    {
        return false;
    }

    /**
     * Проверяет разрешение и область видимости контакта.
     */
    private function hasAccessToContact(User $user, Contact $contact, string $permission): bool {
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
        // Если контакт связан с компанией,
        // дополнительно проверяем доступ к этой компании.
        if ($contact->company_id !== null) {
            $company = $contact->company;
            if ($company === null || ! $user->can('view', $company)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Проверяет, находится ли ответственный за контакт
     * в доступном пользователю отделе.
     */
    private function belongsToAccessibleDepartment(User $user, Contact $contact): bool {
        $owner = $contact->responsible;
        if ($owner === null || $owner->department_id === null) {
            return false;
        }
        return in_array((int) $owner->department_id, $user->accessibleDepartmentIds(), true);
    }
}