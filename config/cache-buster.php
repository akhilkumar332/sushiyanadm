<?php
// /config/cache-buster.php

// Cache file setup
$cacheFile = __DIR__ . '/cache-buster-cache.json';
$cacheTTL = 3600; // 1 hour cache expiration

// Check if cache file exists and is fresh
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTTL) {
    $cacheData = json_decode(file_get_contents($cacheFile), true);
    if ($cacheData && isset($cacheData['mtime']) && isset($cacheData['hash'])) {
        $latestMtime = $cacheData['mtime'];
        $fileHash = $cacheData['hash'];
    } else {
        error_log("Cache buster: Invalid cache file format in $cacheFile");
        $latestMtime = 0;
        $fileHash = '';
    }
} else {
    $files = [
        __DIR__ . '/../css/styles.css',
        __DIR__ . '/../css/style-menu.css',
        __DIR__ . '/../skripte/scripts.js',
        __DIR__ . '/../skripte/translate.js'
    ];

    $latestMtime = 0;
    foreach ($files as $file) {
        if (file_exists($file)) {
            $mtime = filemtime($file);
            if ($mtime > $latestMtime) {
                $latestMtime = $mtime;
            }
        } else {
            error_log("Cache buster: File not found - $file");
        }
    }

    $fileHash = md5(implode('', array_map(function($file) {
        return file_exists($file) ? md5_file($file) : '';
    }, $files)));

    $cacheData = [
        'mtime' => $latestMtime,
        'hash' => $fileHash
    ];
    if (!file_put_contents($cacheFile, json_encode($cacheData, JSON_PRETTY_PRINT))) {
        error_log("Cache buster: Failed to write to $cacheFile");
    }
}

// Set version with reset key
$resetKey = 'reset-v1.9';
$version = ($latestMtime ?: time()) . '-' . $resetKey;

// Make $version and $fileHash available to the including script
$GLOBALS['cacheBusterVersion'] = $version;
$GLOBALS['cacheBusterFileHash'] = $fileHash;
$GLOBALS['cacheBusterResetKey'] = $resetKey;

// Set no-cache headers for this script
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Check if this is an HTML request
$isHtmlRequest = (
    (!isset($_SERVER['HTTP_ACCEPT']) || strpos($_SERVER['HTTP_ACCEPT'], 'text/html') !== false) &&
    (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') &&
    !in_array(basename($_SERVER['SCRIPT_NAME']), ['online-orders.php', 'restore_cart.php', 'api.php']) &&
    !preg_match('/\.(json|api)$/i', $_SERVER['REQUEST_URI'])
);

// Output JavaScript only for HTML requests
if ($isHtmlRequest) {
    echo "<script type='text/javascript'>
        if (!window.hasOwnProperty('cacheBusterInitialized')) {
            window.cacheBusterInitialized = true;

            (function() {
                const currentVersion = '$version';
                const currentFileHash = '$fileHash';
                const cacheKey = 'appCacheVersion';
                const hashKey = 'appFileHash';
                const resetFlagKey = 'cacheResetFlag';

                function clearCookies() {
                    document.cookie.split(';').forEach(cookie => {
                        const name = cookie.split('=')[0].trim();
                        document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/';
                    });
                }

                function clearStorage() {
                    localStorage.clear();
                    sessionStorage.clear();
                }

                async function clearAllCaches() {
                    console.log('Clearing all caches...');
                    clearCookies();
                    clearStorage();
                    if ('caches' in window) {
                        try {
                            const names = await caches.keys();
                            await Promise.all(names.map(name => caches.delete(name)));
                        } catch (err) {
                            console.error('Cache API error:', err);
                        }
                    }
                    if ('serviceWorker' in navigator) {
                        navigator.serviceWorker.getRegistrations().then(regs => regs.forEach(reg => reg.unregister()));
                    }
                    if (window.performance && window.performance.navigation.type !== 2) {
                        window.location.reload(true);
                    }
                }

                const storedVersion = localStorage.getItem(cacheKey);
                const storedHash = localStorage.getItem(hashKey);
                const storedResetFlag = localStorage.getItem(resetFlagKey);

                if (storedResetFlag !== '$resetKey' || storedVersion !== currentVersion || storedHash !== currentFileHash) {
                    console.log('Cache busting triggered: Reset key, version, or hash changed');
                    clearAllCaches();
                    localStorage.setItem(cacheKey, currentVersion);
                    localStorage.setItem(hashKey, currentFileHash);
                    localStorage.setItem(resetFlagKey, '$resetKey');
                    if (!window.location.search.includes('reloaded=true')) {
                        console.log('Forcing reload with ?reloaded=true');
                        window.location = window.location.pathname + '?reloaded=true';
                    }
                }
            })();
        }
    </script>";
}
?>