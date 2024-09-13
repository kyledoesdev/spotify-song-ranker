<?php

it('can load home page', function() {
    $this->get('/')->assertStatus(200);
});

it('can load the about page', function() {
    $this->get('/about')->assertStatus(200);
});

it('can load the explore page', function() {
    $this->get('/explore')->assertStatus(200);
});