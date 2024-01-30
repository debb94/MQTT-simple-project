# Proyecto IoT escritura y suscripción MQTT

Este proyecto usa la libreria php-mqtt/client : https://github.com/php-mqtt/client para instalar puede usar el siguiente comando en tu proyecto: 

    composer require php-mqtt/client

Este proyecto requiere de un servidor de Mosquitto corriendo para el envio y recepcion de mensajes.

# Levantar Mosquitto en Docker.
crea el siguiente directorio en la ruta raiz de tu proyecto.
	
	.config
	
Dentro de este directorio crea el fichero mosquitto.conf y agrega el siguiente codigo:

    listener 1883
    listener 9001
    protocol websockets
    persistence true
    persistence_location /mosquitto/data

Crea el siguiente archivo docker-compose.yaml

	version: '3.9'
	
	services:
	  mosquitto:
	  container_name: mosquitto2
	  image: eclipse-mosquitto
	  restart: always
	  volumes:
		- ./config:/mosquitto/config
		- ./config:/mosquitto/data
		- ./config:/mosquitto/log
	  ports:
	    - 1883:1883
		- 9001:9001
	volumes:
	  config:
	  data:
	  log:

Ahora levanta el contenedor de docker con el siguiente comando:

	docker compose up

Una vez corriendo el contenedor docker, ingresa desde otra terminal al contenedor de la siguiente forma:

	docker exec -it mosquitto sh

y ejecuta el siguiente comando para generar el archivo de credenciales
	
	mosquitto_passwd -c /mosquitto/config/auth USUARIO_A_AUTENTICAR

Este comando te solicitara un password y reingresarlo, una ves hecho esto, creara un fichero dentro del contenedor que conservara las credenciales con la que podrás autenticarte al servidor MQTT.

Ahora da de baja el contenedor docker

	docker compose down

y levantamos nuevamente el contenedor, pero antes modificamos el archivo ./config/moquitto.conf agregando dos lineas adicionales, una que permitira usar las contraseñas creadas previamente y otra para redireccionar los logs a un fichero especifico que despues podamos consultar.

    password_file /mosquitto/config/auth #agregada
    listener 1883
    listener 9001
    protocol websockets
    persistence true
    persistence_location /mosquitto/data
    log_dest file /mosquitto/log/mosquitto.log #agregada


Ahora si levantemos el servidor.
	
	docker compose up


# Envío de mensajes al servidor MQTT con PHP

Fichero send.php
	
	<?php
	require("vendor/autoload.php");
	
	use \PhpMqtt\Client\MqttClient;
	use \PhpMqtt\Client\ConnectionSettings;
	
	$server = "127.0.0.1"; // Cambia esto con la dirección del servidor Mosquitto
	$port = 1883;
	$username = "daniel"; // Si es necesario
	$password = "1234"; // Si es necesario
	$clientId = "phpMQTT-".rand();
	$cleanSession = false;
	$mqttVersion = MqttClient::MQTT_3_1_1;
	
	$connectionSettings = (new  ConnectionSettings)
		->setUsername($username)
		->setPassword($password)
		->setKeepAliveInterval(60)
		->setLastWillTopic('mitopico')
		->setLastWillMessage('client disconnect')
		->setLastWillQualityOfService(1);
		
	$mqtt = new  MqttClient($server, $port, $clientId, $mqttVersion);
	$mqtt->connect($connectionSettings, $cleanSession);

	$mqtt->publish('mitopico',"HELOOOOOO",0,true);
	
	$mqtt->disconnect();

	?>


# Suscripción de mensajes al servidor MQTT con PHP

Fichero suscriber.php

	<?php
	require("vendor/autoload.php");
	
	use \PhpMqtt\Client\MqttClient;
	use \PhpMqtt\Client\ConnectionSettings;
	
	$server = "127.0.0.1"; // Cambia esto con la dirección del servidor Mosquitto
	$port = 1883;
	$username = "daniel"; // Si es necesario
	$password = "1234"; // Si es necesario
	$clientId = "phpMQTT-".rand();
	$cleanSession = false;
	$mqttVersion = MqttClient::MQTT_3_1_1;
	
	$connectionSettings = (new  ConnectionSettings)
		->setUsername($username)
		->setPassword($password)
		->setKeepAliveInterval(60)
		->setLastWillTopic('mitopico')
		->setLastWillMessage('client disconnect')
		->setLastWillQualityOfService(1);
		
	$mqtt = new  MqttClient($server, $port, $clientId, $mqttVersion);
	$mqtt->connect($connectionSettings, $cleanSession);

	$mqtt->subscribe('mitopico', function ($topic, $message) { 
		printf("Received message on topic [%s]: %s\n", $topic, $message);
	}, 0);
	
	$mqtt->loop(true);
	$mqtt->disconnect();

	?>

Ejecute este a traves de una terminal con `php suscriber.php`

Puedes obtener mas detalles de implementacion de las siguientes referencias:
- https://www.emqx.com/en/blog/how-to-use-mqtt-in-php
- https://youtube.com/watch?v=x5GML1FqcTQ
- https://www.youtube.com/watch?v=U8f95agyUJg


