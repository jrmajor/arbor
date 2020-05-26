<?php

it('can be accessed by guests')
    ->get('/')
    ->assertStatus(200);

it('redirects logged users')
    ->withPermissions(0)
    ->get('/')
    ->assertRedirect('/people');
