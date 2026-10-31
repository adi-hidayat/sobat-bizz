<?php

test('the application redirects the root path to the admin panel', function () {
    $response = $this->get('/');

    $response->assertRedirect('/admin');
});
