<?php

test('guests are asked to log in when attempting to view activity log')
    ->get('dashboard/activitylog')
    ->assertStatus(302)
    ->assertRedirect('login');

test('users without permissions cannot view activity log')
    ->withPermissions(3)
    ->get('dashboard/activitylog')
    ->assertStatus(403);

test('users with permissions can view activity log')
    ->withPermissions(4)
    ->get('dashboard/activitylog')
    ->assertStatus(200);
