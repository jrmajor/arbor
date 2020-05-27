<?php

it('can be accessed by guests')
    ->get('/')
    ->assertRedirect('/people');

it('redirects logged users')
    ->withPermissions(0)
    ->get('/')
    ->assertRedirect('/people');
