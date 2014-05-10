Chamber of Commerce retrieval service

It's possible to change the default timeout duration of Guzzle:
```
$c = new Client('', array(Client::REQUEST_OPTIONS => array('timeout' => 0.001, 'connect_timeout' => 0.002)));
```

or
```
$c = new Client();
$c->setDefaultOption('timeout', 0.001);
$c->setDefaultOption('connect_timeout', 0.002);
```