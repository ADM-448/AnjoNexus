<?php
require 'vendor/autoload.php';
$client = new \Goutte\Client();
$crawler = $client->request('GET', 'http://www.finep.gov.br/chamadas-publicas?situacao=aberta');
file_put_contents('finep.html', $crawler->html());
