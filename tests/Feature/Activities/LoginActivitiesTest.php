<?php

test('guests are asked to log in when attempting to view login activites')
    ->get('activities/logins')
    ->assertStatus(302)
    ->assertRedirect('login');

test('users without permissions cannot view login activites')
    ->withPermissions(3)
    ->get('activities/logins')
    ->assertStatus(403);

test('users with permissions can view login activites')
    ->withPermissions(4)
    ->get('activities/logins')
    ->assertStatus(200);
