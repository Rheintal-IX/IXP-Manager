<?php

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Entities\TrafficDailyPhysInt as TrafficDailyPhysIntEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\PhysicalInterface
 */
class PhysicalInterface
{
    const STATUS_CONNECTED       = 1;
    const STATUS_DISABLED        = 2;
    const STATUS_NOTCONNECTED    = 3;
    const STATUS_XCONNECT        = 4;
    const STATUS_QUARANTINE      = 5;

    public static $STATES = array(
        self::STATUS_CONNECTED    => 'Connected',
        self::STATUS_DISABLED     => 'Disabled',
        self::STATUS_NOTCONNECTED => 'Not Connected',
        self::STATUS_XCONNECT     => 'Awaiting X-Connect',
        self::STATUS_QUARANTINE   => 'Quarantine'
    );

    public static $APISTATES = array(
        self::STATUS_CONNECTED    => 'connected',
        self::STATUS_DISABLED     => 'disabled',
        self::STATUS_NOTCONNECTED => 'notconnected',
        self::STATUS_XCONNECT     => 'awaitingxconnect',
        self::STATUS_QUARANTINE   => 'quarantine'
    );

    public static $SPEED = [
        10    => '10 Mbps',
        100   => '100 Mbps',
        1000  => '1 Gbps',
        10000 => '10 Gbps',
        40000 => '40 Gbps',
        100000 => '100 Gbps',
        400000 => '400 Gbps'
    ];

    public static $DUPLEX = array(
        'full'   => 'full',
        'half'   => 'half'
    );


    /**
     * @var integer $status
     */
    protected $status;

    /**
     * @var integer $speed
     */
    protected $speed;

    /**
     * @var string $duplex
     */
    protected $duplex;

    /**
     * @var bool $autoneg
     */
    protected $autoneg = true;


    /**
     * @var string $notes
     */
    protected $notes;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var SwitchPort
     */
    protected $SwitchPort;

    /**
     * @var VirtualInterface
     */
    protected $VirtualInterface;

    /**
     * @var PhysicalInterface
     */
    protected $FanoutPhysicalInterface;

    /**
     * @var PhysicalInterface
     */
    protected $PeeringPhysicalInterface;

    /**
     * @var CoreInterface
     */
    protected $coreInterface;

    /**
     * @var ArrayCollection
     */
    protected $TrafficDailiesPhysInt;


    /**
     * Set status
     *
     * @param integer $status
     * @return PhysicalInterface
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set speed
     *
     * @param integer $speed
     * @return PhysicalInterface
     */
    public function setSpeed($speed)
    {
        $this->speed = $speed;

        return $this;
    }

    /**
     * Get speed
     *
     * @return integer
     */
    public function getSpeed()
    {
        return $this->speed;
    }

    /**
     * Set duplex
     *
     * @param string $duplex
     * @return PhysicalInterface
     */
    public function setDuplex($duplex)
    {
        $this->duplex = $duplex;

        return $this;
    }

    /**
     * Get duplex
     *
     * @return string
     */
    public function getDuplex()
    {
        return $this->duplex;
    }

    /**
     * Set autoneg
     *
     * @param bool $autoneg
     * @return PhysicalInterface
     */
    public function setAutoneg(bool $autoneg): PhysicalInterface {
        $this->autoneg = $autoneg;

        return $this;
    }

    /**
     * Get duplex
     *
     * @return bool
     */
    public function getAutoneg(): bool {
        return $this->autoneg;
    }


    /**
     * Set notes
     *
     * @param string $notes
     * @return PhysicalInterface
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes ?? '';
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @return integer
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }


    /**
     * Set SwitchPort
     *
     * @param SwitchPort $switchPort
     * @return PhysicalInterface
     */
    public function setSwitchPort( SwitchPort $switchPort = null)
    {
        $this->SwitchPort = $switchPort;

        return $this;
    }

    /**
     * Get SwitchPort
     *
     * @return SwitchPort
     */
    public function getSwitchPort()
    {
        return $this->SwitchPort;
    }

    /**
     * Set VirtualInterface
     *
     * @param VirtualInterface $virtualInterface
     * @return PhysicalInterface
     */
    public function setVirtualInterface( VirtualInterface $virtualInterface = null)
    {
        $this->VirtualInterface = $virtualInterface;

        return $this;
    }

    /**
     * Get VirtualInterface
     *
     * @return VirtualInterface
     */
    public function getVirtualInterface() {
        return $this->VirtualInterface;
    }


    /**
     * Set FanoutPhysicalInterface
     *
     * @param PhysicalInterface $fanoutPhysicalInterface
     * @return PhysicalInterface
     */
    public function setFanoutPhysicalInterface( PhysicalInterface $fanoutPhysicalInterface = null)
    {
        $this->FanoutPhysicalInterface = $fanoutPhysicalInterface;

        return $this;
    }

    /**
     * Get FanoutPhysicalInterface
     *
     * @return PhysicalInterface
     */
    public function getFanoutPhysicalInterface()
    {
        return $this->FanoutPhysicalInterface;
    }

    /**
     * Set PeeringPhysicalInterface
     *
     * @param PhysicalInterface $peeringPhysicalInterface
     * @return PhysicalInterface
     */
    public function setPeeringPhysicalInterface( PhysicalInterface $peeringPhysicalInterface = null)
    {
        $this->PeeringPhysicalInterface = $peeringPhysicalInterface;

        return $this;
    }

    /**
     * Get PeeringPhysicalInterface
     *
     * @return PhysicalInterface
     */
    public function getPeeringPhysicalInterface()
    {
        return $this->PeeringPhysicalInterface;
    }

    /**
     * Get CoreInterface
     *
     * @return CoreInterface
     */
    public function getCoreInterface()
    {
        return $this->coreInterface;
    }

    /**
     * Get the core bundle if the physical interface is associated to a core bundle
     *
     * @return CoreBundle
     */
    public function getCoreBundle()
    {
        if( $ci = $this->getCoreInterface() ){
            return $ci->getCoreLink()->getCoreBundle();
        }
        return false;
    }

    /**
     * Get the other physical interface associated to the core link of the current Physical Interface
     *
     * @return PhysicalInterface
     */
    public function getOtherPICoreLink(){

        if( $ci = $this->getCoreInterface() ){
            if( $this->getId() == $ci->getCoreLink()->getCoreInterfaceSideA()->getPhysicalInterface()->getId() ){
                return $ci->getCoreLink()->getCoreInterfaceSideB()->getPhysicalInterface();
            } else {
                return $ci->getCoreLink()->getCoreInterfaceSideA()->getPhysicalInterface();
            }
        }

        return false;


    }

    /**
     * Gets the related peering / fanout port for the current fanout / peering port
     *
     * For reseller functionality, we have the option of having fanout ports connectted to
     * peering ports. In this case, this function will return the related peering or
     * fanout port as appropriate.
     *
     * @return PhysicalInterface The related peering / fanout port (or false for none / n/a)
     */
    public function getRelatedInterface()
    {
        if( $this->getSwitchPort() ){
            if( $this->getSwitchPort()->getType() == SwitchPort::TYPE_FANOUT && $this->getPeeringPhysicalInterface() )
                return $this->getPeeringPhysicalInterface();
            else if( $this->getSwitchPort()->getType() == SwitchPort::TYPE_PEERING && $this->getFanoutPhysicalInterface() )
                return $this->getFanoutPhysicalInterface();
            else
                return false;
        } else{
            return false;
        }

    }


    /**
     * Determine if the port's status is set to CONNECTED
     * @return bool True if the port's status is CONNECTED
     */
    public function statusIsConnected()
    {
        return $this->getStatus() == self::STATUS_CONNECTED;
    }

    /**
     * Determine if the port's status is set to DISABLED
     * @return bool True if the port's status is DISABLED
     */
    public function statusIsDisabled()
    {
        return $this->getStatus() == self::STATUS_DISABLED;
    }

    /**
     * Determine if the port's status is set to NOTCONNECTED
     * @return bool True if the port's status is NOTCONNECTED
     */
    public function statusIsNotConnected()
    {
        return $this->getStatus() == self::STATUS_NOTCONNECTED;
    }

    /**
     * Determine if the port's status is set to XCONNECT
     * @return bool True if the port's status is XCONNECT
     */
    public function statusIsAwaitingXConnect()
    {
        return $this->getStatus() == self::STATUS_XCONNECT;
    }

    /**
     * Determine if the port's status is set to QUARANTINE
     * @return bool True if the port's status is QUARANTINE
     */
    public function statusIsQuarantine()
    {
        return $this->getStatus() == self::STATUS_QUARANTINE;
    }

    /**
     * Determine if the port's status is set to QUARANTINE / CONNECTED
     * @return bool True if the port's status is QUARANTINE / CONNECTED
     */
    public function statusIsConnectedOrQuarantine()
    {
        return $this->getStatus() == self::STATUS_CONNECTED || $this->getStatus() == self::STATUS_QUARANTINE;
    }

    /**
     * Is this port graphable?
     *
     * @return bool
     */
    public function isGraphable(): bool {
        return $this->statusIsConnectedOrQuarantine();
    }

    /**
     * Try to find the most accurate version of the port's speed.
     *
     * I.e. try the actual SNMP-discovered port speed first, otherwise use the configured speed
     *
     * @return int
     */
    public function resolveDetectedSpeed() {
        // try the actual SNMP-discovered port speed first, otherwise use the configured speed:
        return $this->getSwitchPort()->getIfHighSpeed() > 0 ? $this->getSwitchPort()->getIfHighSpeed() : $this->getSpeed();
    }

    /**
     * Turn the database integer representation of the speed into text as
     * defined in the self::$SPEEDS array (or 'Unknown')
     * @return string
     */
    public function resolveSpeed(): string {
        return self::$SPEED[ $this->getSpeed() ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the states into text as
     * defined in the self::$STATES array (or 'Unknown')
     * @return string
     */
    public function resolveStatus(): string {
        return self::$STATES[ $this->getStatus() ] ?? 'Unknown';
    }

    /**
     * Turn the database integer representation of the states into text suitable
     * for API output as defined in the self::$STATES array (or 'unknown')
     * @return string
     */
    public function resolveAPIStatus(): string {
        return self::$APISTATES[ $this->getStatus() ] ?? 'unknown';
    }




    /**
     * Add TrafficDailies
     *
     * @param TrafficDailyPhysIntEntity $trafficDailyPhysInt
     * @return PhysicalInterface
     */
    public function addTrafficDailyPhysInt( TrafficDailyPhysIntEntity $trafficDailyPhysInt )
    {
        $this->TrafficDailiesPhysInt[] = $trafficDailyPhysInt;

        return $this;
    }

    /**
     * Remove TrafficDailies
     *
     * @param TrafficDailyPhysIntEntity $trafficDailyPhysInt
     */
    public function removeTrafficDaily( TrafficDailyPhysIntEntity $trafficDailyPhysInt)
    {
        $this->TrafficDailiesPhysInt->removeElement( $trafficDailyPhysInt );
    }

    /**
     * Get TrafficDailies
     *
     * @return ArrayCollection
     */
    public function getTrafficDailiesPhysInt()
    {
        return $this->TrafficDailiesPhysInt;
    }



}
