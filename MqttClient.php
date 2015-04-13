<?php
/**
 * Copyright (C) 2015, Some right reserved.
 * @author Kacper "Kadet" Donat <kadet1090@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/legalcode CC BY-SA
 *
 * Contact with author:
 * Xmpp: kadet@jid.pl
 * E-mail: kadet1090@gmail.com
 *
 * From Kadet with love.
 */

namespace Kadet\Mqtt;


use Kadet\Mqtt\Packet\Connect;
use Kadet\SocketLib\SocketClient;

class MqttClient extends SocketClient {
    public $username = null;
    public $password = null;

    public $clientId = 'KadetMqttClient';

    public function __construct($address, $port, $timeout = 10)
    {
        parent::__construct($address, $port, 'tcp', $timeout);
    }

    public function connect($will = null, $blocking = false)
    {
        $this->onConnect->add(function (MqttClient $client) use ($will) {
            if(isset($this->logger)) $this->logger->info('Connected to mqtt://'.$this->_address.':'.$this->_port.'.');

            $packet = new Connect($this->clientId);

            $packet->will = $will;
            $packet->username = $this->username;
            $packet->password = $this->password;

            $client->send($packet->__toString());
        });

        parent::connect($blocking);
    }

}