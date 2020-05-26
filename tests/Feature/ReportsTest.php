<?php

test('guest are asked to log in when attempting to view reports')
    ->get('reports')
    ->assertStatus(302)
    ->assertRedirect('login');

test('users without permissions cannot view reports')
    ->withPermissions(3)
    ->get('reports')
    ->assertStatus(403);

test('users with permissions can view reports')
    ->withPermissions(4)
    ->get('reports')
    ->assertStatus(200);
