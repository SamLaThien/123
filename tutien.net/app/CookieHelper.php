<?php

namespace App;

class CookieHelper {
    function updateCookie($oldCookie, $headers)
    {
        if (!empty($headers['Set-Cookie'])) {
            $setCookie = is_array($headers['Set-Cookie']) ? implode(';', $headers['Set-Cookie']) : $headers['Set-Cookie'];
        } elseif (!empty($headers['set-cookie'])) {
            $setCookie = is_array($headers['set-cookie']) ? implode(';', $headers['set-cookie']) : $headers['set-cookie'];
        }
        $setCookie = $this->cleanCookie(explode(';', $setCookie));
        $cookie = explode('; ', $oldCookie);
        // unset($cookie['TAM']);
        $cookie = $this->updateReada($cookie, $setCookie);
        // $cookie = $this->updateReading($cookie, $setCookie);
        // $cookie = $this->updateReadingStory($cookie, $setCookie);
        $cookie = $this->updateCfduid($cookie, $setCookie);
        $cookie = $this->updatePHPSESSID($cookie, $setCookie);
        $cookie = $this->updateTAM($cookie, $setCookie);

        return $cookie;
    }

    private function cleanCookie($cookie)
    {
        for ($i = 0; $i < count($cookie); $i++) {
            $cookie[$i] = trim($cookie[$i], " ");
        }

        return array_values(array_unique($cookie));
    }

    private function updateReada($cookie, $setCookie)
    {
        $cookieIndex = $this->getPropertyIndex($cookie, 'reada');
        $setCookieIndex = $this->getPropertyIndex($setCookie, 'reada');

        return $this->updateCookieData($cookie, $setCookie, $cookieIndex, $setCookieIndex);
    }

    private function updateReading($cookie, $setCookie)
    {
        $cookieIndex = $this->getPropertyIndex($cookie, 'reading');
        $setCookieIndex = $this->getPropertyIndex($setCookie, 'reading');

        return $this->updateCookieData($cookie, $setCookie, $cookieIndex, $setCookieIndex);
    }

    private function updateReadingStory($cookie, $setCookie)
    {
        $cookieIndex = $this->getPropertyIndex($cookie, 'readingstory');
        $setCookieIndex = $this->getPropertyIndex($setCookie, 'readingstory');

        return $this->updateCookieData($cookie, $setCookie, $cookieIndex, $setCookieIndex);
    }

    private function updateUSER($cookie, $setCookie)
    {
        $cookieIndex = $this->getPropertyIndex($cookie, 'USER');
        $setCookieIndex = $this->getPropertyIndex($setCookie, 'USER');

        return $this->updateCookieData($cookie, $setCookie, $cookieIndex, $setCookieIndex);
    }

    private function updateTAM($cookie, $setCookie)
    {
        $cookieIndex = $this->getPropertyIndex($cookie, 'TAM');
        $setCookieIndex = $this->getPropertyIndex($setCookie, 'TAM');

        return $this->updateCookieData($cookie, $setCookie, $cookieIndex, $setCookieIndex);
    }

    private function updatePHPSESSID($cookie, $setCookie)
    {
        $cookieIndex = $this->getPropertyIndex($cookie, 'PHPSESSID');
        $setCookieIndex = $this->getPropertyIndex($setCookie, 'PHPSESSID');

        return $this->updateCookieData($cookie, $setCookie, $cookieIndex, $setCookieIndex);
    }

    private function updateCfduid($cookie, $setCookie)
    {
        $cookieIndex = $this->getPropertyIndex($cookie, 'cf_clearance');
        $setCookieIndex = $this->getPropertyIndex($setCookie, 'cf_clearance');

        return $this->updateCookieData($cookie, $setCookie, $cookieIndex, $setCookieIndex);
    }

    private function updateCookieData($cookie, $setCookie, $cookieIndex, $setCookieIndex)
    {
        if ($cookieIndex != -1 && $setCookieIndex != -1) {
            $cookie[$cookieIndex] = $setCookie[$setCookieIndex]; // Cập nhật
        } else if ($cookieIndex === -1 && $setCookieIndex != -1) {
            $cookie[] = $setCookie[$setCookieIndex]; // Thêm mới
        }

        return $cookie;
    }

    private function getPropertyIndex($arrs, $property)
    {
        for ($i = 0; $i < count($arrs); $i++) {
            $item = $arrs[$i];
            $index = strpos($item, $property);
            if ($index !== false) {
                return $i;
            }
        }

        return -1;
    }
}
