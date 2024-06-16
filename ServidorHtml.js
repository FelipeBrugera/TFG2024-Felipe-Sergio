const mqtt = require('mqtt');
const WebSocket = require('ws');
const fs = require('fs');

// Configuración para AWS IoT Core
const iotEndpoint = 'a2c4axdwxp16sj-ats.iot.us-east-1.amazonaws.com';
const iotPort = 8883;
const iotTopic = 'TFG';
const clientId = 'IdClienteTFGSensor';

// Configuración del servidor WebSocket
const wss = new WebSocket.Server({ port: 9090 });

// Conexión al cliente de AWS IoT Core
const client = mqtt.connect({
  port: iotPort,
  clientId: clientId,
  host: iotEndpoint,
  protocol: 'mqtts',
  key: fs.readFileSync('C:\\Users\\Felipe\\Documents\\Arduino\\M5Stack\\MQTT\\data\\8-private.pem.key'),
  cert: fs.readFileSync('C:\\Users\\Felipe\\Documents\\Arduino\\M5Stack\\MQTT\\data\\8-certificate.pem.crt'),
  ca: fs.readFileSync('C:\\Users\\Felipe\\Documents\\Arduino\\M5Stack\\MQTT\\data\\AmazonRootCA1.pem'),
  rejectUnauthorized: true // Esto es necesario para aceptar certificados de AWS IoT Core
});

client.on('connect', function () {
  console.log('Conectado a AWS IoT Core');
  client.subscribe(iotTopic, function (err) {
    if (!err) {
      console.log('Suscrito al topic ' + iotTopic);
    }
  });
});

client.on('message', function (topic, message) {
  console.log('Mensaje recibido desde AWS IoT Core:', message.toString());
  // Envía el mensaje a todos los clientes WebSocket conectados
  wss.clients.forEach(function (client) {
    if (client.readyState === WebSocket.OPEN) {
      client.send(message.toString());
    }
  });
});

// Manejo de conexiones WebSocket
wss.on('connection', function (ws) {
  console.log('Cliente WebSocket conectado');
});

console.log('Servidor intermedio iniciado');
