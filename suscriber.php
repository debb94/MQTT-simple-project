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
  ->setLastWillTopic('emqx/test/last-will')
  ->setLastWillMessage('client disconnect')
  ->setLastWillQualityOfService(1);

$mqtt = new MqttClient($server, $port, $clientId, $mqttVersion);
$mqtt->connect($connectionSettings, $cleanSession);
printf("client connected\n");

// $mqtt->subscribe("mi-topico",)

$mqtt->subscribe('mitopico', function ($topic, $message) {
    printf("Received message on topic [%s]: %s\n", $topic, $message);
}, 0);

$mqtt->loop(true);
$mqtt->disconnect();