<?php
namespace Tricolore\Http\CookieJar;

use Tricolore\Config\Config;

class CookieJar
{
    /**
     * Set new cookie
     * 
     * @param string $name
     * @param string $value 
     * @param int $expire
     * @return bool
     */
    public function set($name, $value, $expire = 86400)
    {
        return setcookie($name, $value, time() + $expire, 
            Config::key('cookie.path'), 
            Config::key('cookie.domain'), 
            Config::key('cookie.secure')
        );
    }

    /**
     * Get existing cookie
     * 
     * @param string $name
     * @return string|bool
     */
    public function get($name)
    {
        if(isset($_COOKIE[$name])) {
            return trim(str_replace(["\0", "\n", "\t", "\s"], '', $_COOKIE[$name]));
        }

        return false;
    }

    /**
     * Destroy cookie
     * 
     * @param string $name
     * @return bool
     */
    public function destroy($name)
    {
        if(isset($_COOKIE[$name])) {
            unset($_COOKIE[$name]);
        }

        return setcookie($name, null, -1, 
            Config::key('cookie.path'), 
            Config::key('cookie.domain'), 
            Config::key('cookie.secure')
        );
    }
}
