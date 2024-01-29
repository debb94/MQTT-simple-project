<?php

require("vendor/autoload.php");


use \PhpMqtt\Client\MqttClient;
use \PhpMqtt\Client\ConnectionSettings;

$server = "127.0.0.1";  // Cambia esto con la dirección del servidor Mosquitto
$port = 1883;
$username = "daniel";  // Si es necesario
$password = "1234";  // Si es necesario
$clientId = "phpMQTT-".rand();
$cleanSession = false;
$mqttVersion = MqttClient::MQTT_3_1_1;


$connectionSettings = (new ConnectionSettings)
  ->setUsername($username)
  ->setPassword($password)
  ->setKeepAliveInterval(60)
  ->setLastWillTopic('mitopico')
  ->setLastWillMessage('client disconnect')
  ->setLastWillQualityOfService(1);

$mqtt = new MqttClient($server, $port, $clientId, $mqttVersion);
$mqtt->connect($connectionSettings, $cleanSession);

// $mqtt->connect();
// $mqtt->publish("mitopico", "HELOOOOOO!",0);
$mqtt->publish('mitopico',"HELOOOOOO",0,true);
// $mqtt->publish('php-mqtt/client/test', 'Hello World!', 0);
$mqtt->disconnect();

?>