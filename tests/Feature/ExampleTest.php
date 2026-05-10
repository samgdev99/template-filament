<?php

test('the application redirects root to admin panel', function () {
    $response = $this->get('/');

    $response->assertRedirect('/admin');
});
