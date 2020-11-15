<?php
/**
 * Rewritten base method
 *
 * Header Redirect
 *
 * Header redirect in two flavors
 * For very fine grained control over headers, you could use the Output
 * Library's set_header() function.
 *
 * @param    string $uri URL
 * @param    string $method Redirect method
 *            'auto', 'location' or 'refresh'
 * @param    int $code HTTP Response status code
 * @return    void
 */
function redirect($uri = '', $method = 'auto', $code = NULL)
{
    $controller = &get_instance();
    $controller->destruct();

    if (!preg_match('#^(\w+:)?//#i', $uri)) {
        $uri = site_url($uri);
    }

    // IIS environment likely? Use 'refresh' for better compatibility
    if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false) {
        $method = 'refresh';
    } elseif ($method !== 'refresh' && (empty($code) || !is_numeric($code))) {
        if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1') {
            $code = ($_SERVER['REQUEST_METHOD'] !== 'GET')
                ? 303    // reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
                : 307;
        } else {
            $code = 302;
        }
    }

    switch ($method) {
        case 'refresh':
            header('Refresh:0;url=' . $uri);
            break;
        default:
            header('Location: ' . $uri, true, $code);
            break;
    }
    exit;
}

if (!function_exists('redirectReferer')) {
    /**
     * Redirect referer
     */
    function redirectReferer()
    {
        $controller = &get_instance();
        $controller->destruct();

        header('Location: ' . $_SERVER['HTTP_REFERER'], true, 303);
        exit;
    }
}