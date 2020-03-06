<?php
namespace carlonicora\minimalism\databases\security;

use carlonicora\minimalism\databases\security\tables\auth;
use carlonicora\minimalism\databases\security\tables\clients;
use carlonicora\minimalism\exceptions\dbConnectionException;
use carlonicora\minimalism\factories\databaseFactory;

class securityDb {
    /** @var auth */
    private static $auth;

    /** @var clients */
    private static $clients;

    /**
     * @return null|auth
     */
    public static function auth(): ?auth {
        if (!isset(self::$auth)){
            try {
                self::$auth = databaseFactory::create(auth::class);
            } catch (dbConnectionException $e) {
                self::$auth = null;
            }
        }
        return self::$auth;
    }

    /**
     * @return null|clients
     */
    public static function clients(): ?clients {
        if (!isset(self::$clients)){
            try {
                self::$clients = databaseFactory::create(clients::class);
            } catch (dbConnectionException $e) {
                self::$clients = null;
            }
        }
        return self::$clients;
    }
}