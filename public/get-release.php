<?php

function get_release()
{
    if (file_exists('.cache')) {
        $cacheDate = (new Datetime())->setTimestamp(filemtime('.cache'));
        $invalidDate = new Datetime('yesterday');
        if ($cacheDate<=$invalidDate) {
            error_log('Cache outdated.');
            $result = do_get_release();
            file_put_contents('.cache', $result);
        } else {
            error_log('Retrieve from cache.');
            $result = file_get_contents('.cache');
        }
    } else {
        error_log('No cache. Rebuild it.');
        $result = do_get_release();
        file_put_contents('.cache', $result);
    }

    return json_decode($result);
}

function do_get_release()
{
    $ch = curl_init();
    $url = "https://api.github.com/repos/millesime/millesime/releases/latest";
    if (getenv('GITHUB_CLIENTID')!==false && getenv('GITHUB_CLIENTSECRET')!==false) {
        $url.= sprintf(
            "?client_id=%s&client_secret=%s",
            getenv('GITHUB_CLIENTID'),
            getenv('GITHUB_CLIENTSECRET')
        );
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: millesime/website',
        'Accept: application/json',
    ]);

    if (getenv('http_proxy')!==false) {
        $proxy = parse_url(getenv('http_proxy'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
        curl_setopt($ch, CURLOPT_PROXY, $proxy['host']);
        curl_setopt($ch, CURLOPT_PROXYPORT, $proxy['port']);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, implode(':', [$proxy['user'], $proxy['pass']]));
    }

    $result = curl_exec($ch);

    error_log(curl_getinfo($ch, CURLINFO_HEADER_OUT).$result);

    return $result;
}

function get_asset($release, $name)
{
    $result = null;
    foreach ($release->assets as $asset) {
        if ($asset->name == $name) {
            $result = $asset;
        }
    }
    return $result;
}
