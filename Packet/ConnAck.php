<?php
/**
 * Copyright (C) 2015, Some right reserved.
 *
 * @author  Kacper "Kadet" Donat <kadet1090@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/legalcode CC BY-SA
 *
 * Contact with author:
 * Xmpp: kadet@jid.pl
 * E-mail: kadet1090@gmail.com
 *
 * From Kadet with love.
 */

namespace Kadet\Mqtt\Packet;


class ConnAck extends MqttPacket
{
    public $sessionPresent;
    public $code;

    const ACCEPTED                      = 0x00;
    const UNACCEPTABLE_PROTOCOL_VERSION = 0x01;
    const IDENTIFIER_REJECTED           = 0x02;
    const SERVER_UNAVAILABLE            = 0x03;
    const BAD_USERNAME_OR_PASSWORD      = 0x04;
    const NOT_AUTHORIZED                = 0x05;

    public function __construct()
    {
        $this->_type = parent::CONNACK;
    }

    public static function parse($packet)
    {
        $self = new ConnAck();

        $self->sessionPresent = (bool)($packet[0] & 1);
        $self->code           = ord($packet[1]);

        return $self;
    }

    public function __debugInfo()
    {
        $info = parent::__debugInfo();
        $info['various'] = [
            'session-present' => $this->sessionPresent
        ];

        switch($this->code) {
            case self::ACCEPTED                     : $info['various']['return-code'] = 'ConnAck::ACCEPTED (0x00)'; break;
            case self::UNACCEPTABLE_PROTOCOL_VERSION: $info['various']['return-code'] = 'ConnAck::UNACCEPTABLE_PROTOCOL_VERSION (0x01)'; break;
            case self::IDENTIFIER_REJECTED          : $info['various']['return-code'] = 'ConnAck::IDENTIFIER_REJECTED (0x02)'; break;
            case self::SERVER_UNAVAILABLE           : $info['various']['return-code'] = 'ConnAck::SERVER_UNAVAILABLE (0x03)'; break;
            case self::BAD_USERNAME_OR_PASSWORD     : $info['various']['return-code'] = 'ConnAck::BAD_USERNAME_OR_PASSWORD (0x04)'; break;
            case self::NOT_AUTHORIZED               : $info['various']['return-code'] = 'ConnAck::NOT_AUTHORIZED (0x05)'; break;
        }

        return $info;
    }

    public function _isset_payload()
    {
        return false;
    }

    public function _isset_various()
    {
        return true;
    }

    public function _get_various() {
        return chr($this->sessionPresent).chr($this->code);
    }
}