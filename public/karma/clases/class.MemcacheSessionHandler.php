<?php
class MemcacheSessionHandler
{
    private static $lifetime = 0;
    public static $memcache;
    public static $host = "localhost";
    public static $port = 11211;

    public static function open()
    {
        self::$lifetime = ini_get('session.gc_maxlifetime');
        if (!(self::$memcache instanceof Memcache)) {
            self::$memcache = new Memcache();
        }
        self::$memcache->connect(self::$host, self::$port);
        return true;
    }

    public static function read($id)
    {
        return self::$memcache->get("sessions/{$id}", MEMCACHE_COMPRESSED);
    }

    public static function write($id, $data)
    {
        return self::$memcache->set("sessions/{$id}", $data, MEMCACHE_COMPRESSED, self::$lifetime);
    }

    public static function destroy($id)
    {
        return self::$memcache->delete("sessions/{$id}");
    }

    private function __construct(){}
    public static function gc(){ return true; }
    public static function close(){    return true; }
    public function __destruct()
    {
        self::$memcache->close();
        session_write_close();
    }
}