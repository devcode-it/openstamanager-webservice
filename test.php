<?php

require_once __DIR__.'/vendor/autoload.php';

$client = new OSM\API\Client('http://localhost/openstamanager/api/');
$client->login('admin', 'admin');

$retrieve = $client->retrieve('an_anagrafiche');
print_r($retrieve);

$create = $client->create('add_anagrafica', [
    'data' => [
        'ragione_sociale' => 'TETETETE',
        'tipi' => [1, 2],
    ],
]);
print_r($create);

$update = $client->update('update_anagrafica', [
    'id' => 34,
    'data' => [
        'ragione_sociale' => 'LLLALALA',
        'tipi' => [2, 4],
    ],
]);
print_r($update);

$delete = $client->delete('delete_anagrafica', [
    'id' => 33,
]);
print_r($delete);

$client->logout();