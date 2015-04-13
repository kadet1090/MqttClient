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

namespace Kadet\Mqtt\Packet;


class Connect extends MqttPacket {
    public $clean = true;
    public $will = null;

    public $username = null;
    public $password = null;

    public $keepAlive = 10;

    protected $_clientId;


    public function __construct($clientId, $clean = true) {
        parent::__construct(MqttPacket::CONNECT);

        $this->_clientId = $clientId;

        $this->_flags = 0x00;
        $this->identifier = 0x0004;
    }

    public function _isset_payload()
    {
        return true;
    }

    public function _isset_various()
    {
        return true;
    }

    public function _get_payload()
    {
        $result = $this->encodeString($this->_clientId);

        if(isset($this->will)) {
            $result .= $this->encodeString($this->will['topic']);
            $result .= $this->encodeString($this->will['message']);
        }

        if($this->username !== null) $result .= $this->encodeString($this->username);
        if($this->password !== null) $result .= $this->encodeString($this->password);

        return $result;
    }

    public function _get_various()
    {
        $result = 'MQTT';
        $result .= chr(0x04);

        # flag
        $flag = 0x00;
        if($this->username !== null) $flag += 0x80;
        if($this->password !== null) $flag += 0x40;

        if($this->clean) $flag += 0x02;

        if(isset($this->will)) {
            $flag += 0x04;
            $flag += ($this->will['QoS'] << 3) & bindec('11');
            if($this->will['retain']) $flag += 0x20;
        }

        $result .= chr($flag);
        $result .= $this->encodeInt16($this->keepAlive);

        return $result;
    }

    public function __debugInfo()
    {
        $info = parent::__debugInfo();
        $info['various'] = [];

        $info['various']['protocol-name'] = 'MQTT';
        $info['various']['protocol-level'] = '4';

        $info['various']['flags'] = [
            'clean' => $this->clean,
            'will'  => isset($this->will),
            'will-qos' => isset($this->will['qos']) ? $this->will['qos'] : false,
            'will-retain' => isset($this->will['retain']) ? $this->will['retain'] : false,
            'username' => isset($this->username),
            'password' => isset($this->password)
        ];

        $info['various']['keep-alive'] = $this->keepAlive;

        $info['payload'] = [
            'client-id' => $this->_clientId
        ];

        if(isset($this->will)) {
            $info['payload']['will-topic'] = $this->will['topic'];
            $info['payload']['will-message'] = $this->will['message'];
        }

        if($this->username !== null) $info['payload']['username'] .= $this->username;
        if($this->password !== null) $info['payload']['password'] .= $this->password;

        return $info;
    }


}