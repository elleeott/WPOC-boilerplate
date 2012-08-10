<?php

# hmac_sha1.php - HMAC-SHA1 implementation
# Version 1.0
# ---------------------------------------- 
# Copyright (c) 2006 Dwayne C. Litzenberger <dlitz@dlitz.net>
#
# Permission is hereby granted, free of charge, to any person obtaining
# a copy of this software and associated documentation files (the
# "Software"), to deal in the Software without restriction, including
# without limitation the rights to use, copy, modify, merge, publish,
# distribute, sublicense, and/or sell copies of the Software, and to
# permit persons to whom the Software is furnished to do so, subject to
# the following conditions:
# 
# The above copyright notice and this permission notice shall be
# included in all copies or substantial portions of the Software.
# 
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
# EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
# MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
# IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
# CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
# TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
# SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
# ---------------------------------------- 
# HMAC is:
#       H(K XOR opad, H(K XOR ipad, plaintext))
# where ipad is 0x36 repated B times, and opad is 0x5C repeated B times.
# B is the block length of the hash function (for SHA-1, B=64)
# Keys longer than B are hashed before being used.
#
# See "HMAC: Keyed-Hashing for Message Authentication" (RFC 2104)
#       http://www.ietf.org/rfc/rfc2104.txt

function hmac_sha1($data, $key, $raw_output=FALSE) {
    $block_size = 64;   // SHA-1 block size

    if (strlen($key) > $block_size) {
        $k = pack("H*", sha1($key));
    } else {
        $k = str_pad($key, $block_Size, "\x00", STR_PAD_RIGHT);
    }

    $ki = '';
    for ($i = 0; $i < $block_size; $i++) {
        $ki .= chr(ord(substr($k, $i, 1)) ^ 0x36);
    }
    $ko = '';
    for ($i = 0; $i < $block_size; $i++) {
        $ko .= chr(ord(substr($k, $i, 1)) ^ 0x5C);
    }

    $h = sha1($ko . pack('H*', sha1($ki . $data)));
    if ($raw_output) {
        return pack('H*', $h);
    } else {
        return $h;
    }
}

/*
 * PressboxOauth is strongly derivative of:
 *
 * * Abraham Williams (abraham@abrah.am) http://abrah.am
 * *
 * * The first PHP Library to support OAuth for Twitter's REST API.
 *
 * Thanks Abraham!
 */

if( function_exists("plugin_dir_path")) {
    require_once( plugin_dir_path(__FILE__) . 'WP_OAuth.php' );
} else {
    require_once( "./includes/WP_OAuth.php" );
}

if (!class_exists('PressboxOauth')) {

    class PressboxOauth {
        /* Contains the last HTTP status code returned. */

        public $http_code;
        /* Contains the last API call. */
        public $url;
        /* Set up the API root URL. */
        public $host = "https://api.dropbox.com/0/";
        public $filehost = "https://api-content.dropbox.com/0/";

        /* Set timeout default. */
        public $timeout = 30;
        /* Set connect timeout. */
        public $connecttimeout = 30;
        /* Verify SSL Cert. */
        public $ssl_verifypeer = FALSE;
        /* Respons format. */
        public $format = 'json';
        /* Decode returned json data. */
        public $decode_json = false;
        /* Contains the last HTTP headers returned. */
        public $http_info;
        /* Set the useragnet. */
        public $useragent = 'PressboxOauth v0.1';
        /* Immediately retry the API call if the response was not successful. */
        //public $retry = TRUE;

        /**
         * Set API URLS
         */
        function accessTokenURL() {
            return 'https://api.dropbox.com/0/oauth/access_token';
        }

        function authorizeURL() {
            return 'https://www.dropbox.com/0/oauth/authorize';
        }

        function requestTokenURL() {
            return 'https://api.dropbox.com/0/oauth/request_token';
        }

        /**
         * construct PressboxOAuth object
         */
        function __construct($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL) {
            $this->sha1_method = new WPOAuthSignatureMethod_HMAC_SHA1();
            $this->consumer = new WPOAuthConsumer($consumer_key, $consumer_secret);
            if (!empty($oauth_token) && !empty($oauth_token_secret)) {
                $this->token = new WPOAuthConsumer($oauth_token, $oauth_token_secret);
            } else {
                $this->token = NULL;
            }
        }

        /**
         * Get a request_token from Dropbox
         *
         * @returns a key/value array containing oauth_token and oauth_token_secret
         */
        function getRequestToken($oauth_callback = NULL) {
            $parameters = array();
            if (!empty($oauth_callback)) {
                $parameters['oauth_callback'] = $oauth_callback;
            }
            $request = $this->oAuthRequest($this->requestTokenURL(), 'GET', $parameters);
            $token = WPOAuthUtil::parse_parameters($request);
            $this->token = new WPOAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
            return $token;
        }

        /**
         * Get the authorize URL
         *
         * @returns a string
         */
        function getAuthorizeURL($token, $callback) {
            if (is_array($token)) {
                $token = $token['oauth_token'];
            }

            return $this->authorizeURL() . "?oauth_token={$token}&oauth_callback=" . urlencode($callback);
        }

        /**
         * Exchange request token and secret for an access token and
         * secret, to sign API calls.
         *
         */
        function getAccessToken($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret) {
            $parameters = array();
            $parameters['oauth_consumer_key'] = $consumer_key;
            $parameters['oauth_token'] = $oauth_token;
            $parameters['oauth_signature_method'] = "HMAC-SHA1";
            $parameters['oauth_nonce'] = wp_create_nonce("pressbox-oauth-request");
            $parameters['oauth_timestamp'] = time();
            $parameters['oauth_version'] = "1.0";

            $signature_base_string =
                    "GET&" . urlencode($this->accessTokenURL()) .
                    "&oauth_consumer_key=" . urlencode($parameters['oauth_consumer_key']) .
                    "&oauth_nonce=" . urlencode($parameters['oauth_nonce']) .
                    "&oauth_signature_method" . urlencode($parameters['oauth_signature_method']) .
                    "&oauth_timestamp" . urlencode($parameters['oauth_timestamp']) .
                    "&oauth_token" . urlencode($parameters['oauth_token']) .
                    "&oauth_version" . urlencode($parameters['oauth_version']);
            $key = urlencode($consumer_secret) . "&" . urlencode($oauth_token_secret);

            $parameters['oauth_signature'] = urlencode(hmac_sha1($signature_base_string, $key, TRUE));

            $request = $this->oAuthRequest($this->accessTokenURL(), 'GET', $parameters);
            $token = WPOAuthUtil::parse_parameters($request);
            $this->token = new WPOAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
            return $token;
        }

        /**
         * GET wrapper for oAuthRequest.
         */
        function get($url, $parameters = array(), $use_file_host = FALSE) {
            $response = $this->oAuthRequest($url, 'GET', $parameters, $use_file_host);
            if ($this->format === 'json' && $this->decode_json) {
                return json_decode($response);
            }
            return $response;
        }

        /**
         * POST wrapper for oAuthRequest.
         */
        function post($url, $parameters = array(), $use_file_host = FALSE) {
            $response = $this->oAuthRequest($url, 'POST', $parameters, $use_file_host);
            if ($this->format === 'json' && $this->decode_json) {
                return json_decode($response);
            }
            return $response;
        }

        /**
         * DELETE wrapper for oAuthReqeust.
         */
        function delete($url, $parameters = array()) {
            $response = $this->oAuthRequest($url, 'DELETE', $parameters);
            if ($this->format === 'json' && $this->decode_json) {
                return json_decode($response);
            }
            return $response;
        }

        /**
         * Format and sign an OAuth / API request
         */
        function oAuthRequest($url, $method, $parameters, $use_file_host = FALSE) {
            if (strrpos($url, 'https://') !== 0 && strrpos($url, 'http://') !== 0) {
                if ($use_file_host) {
                    $url = "{$this->filehost}{$url}";
                } else {
                    $url = "{$this->host}{$url}";
                }
            }
            $request = WPOAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $parameters);
            $request->sign_request($this->sha1_method, $this->consumer, $this->token);
            switch ($method) {
                case 'GET':
                    return $this->http($request->to_url(), 'GET');
                default:
                    return $this->http($request->get_normalized_http_url(), $method, $request->to_postdata());
            }
        }

        /**
         * Make an HTTP request
         *
         * @return API results
         */
        function http($url, $method, $postfields = NULL) {
            $this->http_info = array();
            $ci = curl_init();
            /* Curl settings */
            curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
            curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
            curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
            curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ci, CURLOPT_HTTPHEADER, array('Expect:'));
            curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
            curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
            curl_setopt($ci, CURLOPT_HEADER, FALSE);

            switch ($method) {
                case 'POST':
                    curl_setopt($ci, CURLOPT_POST, TRUE);
                    if (!empty($postfields)) {
                        curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                    }
                    break;
                case 'DELETE':
                    curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    if (!empty($postfields)) {
                        $url = "{$url}?{$postfields}";
                    }
            }

            curl_setopt($ci, CURLOPT_URL, $url);
            $response = curl_exec($ci);
            $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
            $this->http_info = array_merge($this->http_info, curl_getinfo($ci));
            $this->url = $url;
            curl_close($ci);
            return $response;
        }

        /**
         * Get the header info to store.
         */
        function getHeader($ch, $header) {
            $i = strpos($header, ':');
            if (!empty($i)) {
                $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
                $value = trim(substr($header, $i + 2));
                $this->http_header[$key] = $value;
            }
            return strlen($header);
        }

    }

} // PressboxOauth Exists
?>
