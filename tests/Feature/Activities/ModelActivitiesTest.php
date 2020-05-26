<?php

test('guests are asked to log in when attempting to view model activites')
    ->get('activities/models')
    ->assertStatus(302)
    ->assertRedirect('login');

test('users without permissions cannot view model activites')
    ->withPermissions(3)
    ->get('activities/models')
    ->assertStatus(403);

test('users with permissions can view model activites')
    ->withPermissions(4)
    ->get('activities/models')
    ->assertStatus(200);
