<?php

namespace Proxies\__CG__\Entities;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class UserRememberTokens extends \Entities\UserRememberTokens implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Proxy\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Proxy\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Proxy\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = [];



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return ['__isInitialized__', '' . "\0" . 'Entities\\UserRememberTokens' . "\0" . 'token', '' . "\0" . 'Entities\\UserRememberTokens' . "\0" . 'device_name', '' . "\0" . 'Entities\\UserRememberTokens' . "\0" . 'device', '' . "\0" . 'Entities\\UserRememberTokens' . "\0" . 'created', '' . "\0" . 'Entities\\UserRememberTokens' . "\0" . 'expires', '' . "\0" . 'Entities\\UserRememberTokens' . "\0" . 'id', '' . "\0" . 'Entities\\UserRememberTokens' . "\0" . 'User'];
        }

        return ['__isInitialized__', '' . "\0" . 'Entities\\UserRememberTokens' . "\0" . 'token', '' . "\0" . 'Entities\\UserRememberTokens' . "\0" . 'device_name', '' . "\0" . 'Entities\\UserRememberTokens' . "\0" . 'device', '' . "\0" . 'Entities\\UserRememberTokens' . "\0" . 'created', '' . "\0" . 'Entities\\UserRememberTokens' . "\0" . 'expires', '' . "\0" . 'Entities\\UserRememberTokens' . "\0" . 'id', '' . "\0" . 'Entities\\UserRememberTokens' . "\0" . 'User'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function ( UserRememberTokens $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', []);
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', []);
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getToken(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getToken', []);

        return parent::getToken();
    }

    /**
     * {@inheritDoc}
     */
    public function setToken(string $token): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setToken', [$token]);

        parent::setToken($token);
    }

    /**
     * {@inheritDoc}
     */
    public function getDeviceName(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDeviceName', []);

        return parent::getDeviceName();
    }

    /**
     * {@inheritDoc}
     */
    public function setDeviceName(string $device_name): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDeviceName', [$device_name]);

        parent::setDeviceName($device_name);
    }

    /**
     * {@inheritDoc}
     */
    public function getDevice(): string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDevice', []);

        return parent::getDevice();
    }

    /**
     * {@inheritDoc}
     */
    public function setDevice(string $device): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDevice', [$device]);

        parent::setDevice($device);
    }

    /**
     * {@inheritDoc}
     */
    public function getCreated(): \Datetime
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCreated', []);

        return parent::getCreated();
    }

    /**
     * {@inheritDoc}
     */
    public function setCreated(\Datetime $created): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCreated', [$created]);

        parent::setCreated($created);
    }

    /**
     * {@inheritDoc}
     */
    public function getExpires(): \Datetime
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getExpires', []);

        return parent::getExpires();
    }

    /**
     * {@inheritDoc}
     */
    public function setExpires(\Datetime $expires): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setExpires', [$expires]);

        parent::setExpires($expires);
    }

    /**
     * {@inheritDoc}
     */
    public function getId(): int
    {
        if ($this->__isInitialized__ === false) {
            return  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', []);

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function getUser(): \Entities\User
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUser', []);

        return parent::getUser();
    }

    /**
     * {@inheritDoc}
     */
    public function setUser(\Entities\User $User): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUser', [$User]);

        parent::setUser($User);
    }

}
