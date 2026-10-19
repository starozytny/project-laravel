<?php

use App\Enums\UserRole;

test('inherits roles directly and through other roles', function (UserRole $role, array $inherited) {
    expect($role->allInheritedRoles())->toEqualCanonicalizing($inherited);
})->with([
    'developer' => [UserRole::Developer, [UserRole::Admin, UserRole::User]],
    'admin' => [UserRole::Admin, [UserRole::User]],
    'user' => [UserRole::User, []],
]);

test('lists the roles granting the privileges of a role', function (UserRole $role, array $granting) {
    expect($role->rolesGranting())->toEqualCanonicalizing($granting);
})->with([
    'developer' => [UserRole::Developer, [UserRole::Developer]],
    'admin' => [UserRole::Admin, [UserRole::Admin, UserRole::Developer]],
    'user' => [UserRole::User, [UserRole::User, UserRole::Admin, UserRole::Developer]],
]);

test('never inherits from itself', function (UserRole $role) {
    expect($role->allInheritedRoles())->not->toContain($role);
})->with(UserRole::cases());
