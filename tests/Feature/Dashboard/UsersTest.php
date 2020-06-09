<?php

test('guests are asked to log in when attempting to view users page')
    ->get('dashboard/users')
    ->assertStatus(302)
    ->assertRedirect('login');

test('users without permissions cannot view users page')
    ->withPermissions(3)
    ->get('dashboard/users')
    ->assertStatus(403);

test('users with permissions can view users page')
    ->withPermissions(4)
    ->get('dashboard/users')
    ->assertStatus(200);
