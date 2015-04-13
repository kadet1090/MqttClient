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


use Kadet\Utils\Property;

/**
 * Class MqttPacket
 *
 * @package Kadet\Mqtt\Packet
 *
 * @property string $_payload Packets payload
 * @property string $_various Packets various header
 */
abstract class MqttPacket
{
    use Property;

    # Possible packet types
    const CONNECT   = 0x10;
    const CONNACK   = 0x20;
    const PUBLISH   = 0x30;
    const PUBACK    = 0x40;
    const PUBREC    = 0x50;
    const PUBREL    = 0x60;
    const PUBCOMP   = 0x70;
    const SUBSCRIBE = 0x80;
    const SUBACK    = 0x90;
    const UNSUB     = 0xA0;
    const UNSUBACK  = 0xB0;
    const PINGREQ   = 0xC0;
    const PINGRESP  = 0xD0;
    const DISCONN   = 0xE0;

    # fixed header
    protected $_type  = null;
    protected $_flags = null;

    # various header
    public $identifier = null;

    public function __construct($type) {
        $this->_type = $type;
    }

    public function __toString() {
        $result = '';

        # fixed header
        $result .= chr($this->_type + $this->_flags);
        $result .= $this->_remainingLength();

        if($this->identifier !== null)
            $result .= $this->encodeInt16($this->identifier);

        if(isset($this->various))
            $result .= $this->various;

        if(isset($this->payload))
            $result .= $this->payload;

        return $result;
    }

    protected function _remainingLength() {
        $length = ($this->identifier === null ? 0 : 2) + strlen($this->various);
        $length += strlen($this->payload);

        $result = '';
        do {
            $digit = $length % 128;
            $length = $length >> 7;

            if($length > 0)
                $digit = $digit | 0x80;

            $result .= chr($digit);
        } while($length > 0);

        return $result;
    }

    public function _get_payload() {
        return null;
    }

    public function _get_various() {
        return null;
    }

    public abstract function _isset_payload();
    public abstract function _isset_various();

    public function encodeInt16($int) {
        if(($int & 0xFFFF) != $int)
            throw new \InvalidArgumentException("\$int must be 16-bit integer value.");

        return chr($int >> 8).chr($int & 0xFF);
    }

    public function encodeString($string) {
        return $this->encodeInt16(strlen($string)).$string;
    }

    public static function parse($packet) {

    }
}