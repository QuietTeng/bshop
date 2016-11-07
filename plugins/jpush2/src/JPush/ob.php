<?php

class ob {
    private $appKey;
    private $masterSecret;
    private $retryTimes;
    private $logFile;

    public function __construct($appKey, $masterSecret, $logFile=_config::DEFAULT_LOG_FILE, $retryTimes=_config::DEFAULT_MAX_RETRY_TIMES) {
        if (!is_string($appKey) || !is_string($masterSecret)) {
            throw new InvalidArgumentException("Invalid appKey or masterSecret");
        }
        $this->appKey = $appKey;
        $this->masterSecret = $masterSecret;
        if (!is_null($retryTimes)) {
            $this->retryTimes = $retryTimes;
        } else {
            $this->retryTimes = 1;
        }
        $this->logFile = $logFile;
    }

    public function push() { return new PushPayload($this); }
    public function report() { return new ReportPayload($this); }
    public function device() { return new DevicePayload($this); }
    public function schedule() { return new SchedulePayload($this);}

    public function getAuthStr() { return $this->appKey . ":" . $this->masterSecret; }
    public function getRetryTimes() { return $this->retryTimes; }
    public function getLogFile() { return $this->logFile; }
}

class _config {
    const DISABLE_SOUND = "_disable_Sound";
    const DISABLE_BADGE = 0x10000;
    const USER_AGENT = 'JPush-API-PHP-Client';
    const CONNECT_TIMEOUT = 20;
    const READ_TIMEOUT = 120;
    const DEFAULT_MAX_RETRY_TIMES = 3;
    const DEFAULT_LOG_FILE = "./jpush.log";
    const HTTP_GET = 'GET';
    const HTTP_POST = 'POST';
    const HTTP_DELETE = 'DELETE';
    const HTTP_PUT = 'PUT';
}

class PushPayload {

    private static $EFFECTIVE_DEVICE_TYPES = array('ios', 'android', 'winphone');
    const PUSH_URL = 'https://api.jpush.cn/v3/push';
    const PUSH_VALIDATE_URL = 'https://api.jpush.cn/v3/push/validate';

    private $client;
    private $platform;

    private $audience;
    private $tags;
    private $tagAnds;
    private $alias;
    private $registrationIds;

    private $notificationAlert;
    private $iosNotification;
    private $androidNotification;
    private $winPhoneNotification;
    private $smsMessage;
    private $message;
    private $options;

    /**
     * PushPayload constructor.
     * @param $client JPush
     */
    function __construct($client) {
        $this->client = $client;
    }

    public function setPlatform($platform) {
        # $required_keys = array('all', 'android', 'ios', 'winphone');
        if (is_string($platform)) {
            $ptf = strtolower($platform);
            if ('all' === $ptf) {
                $this->platform = 'all';
            } elseif (in_array($ptf, self::$EFFECTIVE_DEVICE_TYPES)) {
                $this->platform = array($ptf);
            }
        } elseif (is_array($platform)) {
            $ptf = array_map('strtolower', $platform);
            $this->platform = array_intersect($ptf, self::$EFFECTIVE_DEVICE_TYPES);
        }
        return $this;
    }

    public function setAudience($all) {
        if (strtolower($all) === 'all') {
            $this->addAllAudience();
            return $this;
        } else {
            throw new InvalidArgumentException('Invalid audience value');
        }
    }

    public function addAllAudience() {
        $this->audience = "all";
        return $this;
    }

    public function addTag($tag) {
        if (is_null($this->tags)) {
            $this->tags = array();
        }

        if (is_array($tag)) {
            foreach($tag as $_tag) {
                if (!is_string($_tag)) {
                    throw new InvalidArgumentException("Invalid tag value");
                }
                if (!in_array($_tag, $this->tags)) {
                    array_push($this->tags, $_tag);
                }
            }
        } else if (is_string($tag)) {
            if (!in_array($tag, $this->tags)) {
                array_push($this->tags, $tag);
            }
        } else {
            throw new InvalidArgumentException("Invalid tag value");
        }

        return $this;

    }

    public function addTagAnd($tag) {
        if (is_null($this->tagAnds)) {
            $this->tagAnds = array();
        }

        if (is_array($tag)) {
            foreach($tag as $_tag) {
                if (!is_string($_tag)) {
                    throw new InvalidArgumentException("Invalid tag_and value");
                }
                if (!in_array($_tag, $this->tagAnds)) {
                    array_push($this->tagAnds, $_tag);
                }
            }
        } else if (is_string($tag)) {
            if (!in_array($tag, $this->tagAnds)) {
                array_push($this->tagAnds, $tag);
            }
        } else {
            throw new InvalidArgumentException("Invalid tag_and value");
        }

        return $this;
    }

    public function addAlias($alias) {
        if (is_null($this->alias)) {
            $this->alias = array();
        }

        if (is_array($alias)) {
            foreach($alias as $_alias) {
                if (!is_string($_alias)) {
                    throw new InvalidArgumentException("Invalid alias value");
                }
                if (!in_array($_alias, $this->alias)) {
                    array_push($this->alias, $_alias);
                }
            }
        } else if (is_string($alias)) {
            if (!in_array($alias, $this->alias)) {
                array_push($this->alias, $alias);
            }
        } else {
            throw new InvalidArgumentException("Invalid alias value");
        }

        return $this;
    }

    public function addRegistrationId($registrationId) {
        if (is_null($this->registrationIds)) {
            $this->registrationIds = array();
        }

        if (is_array($registrationId)) {
            foreach($registrationId as $_registrationId) {
                if (!is_string($_registrationId)) {
                    throw new InvalidArgumentException("Invalid registration_id value");
                }
                if (!in_array($_registrationId, $this->registrationIds)) {
                    array_push($this->registrationIds, $_registrationId);
                }
            }
        } else if (is_string($registrationId)) {
            if (!in_array($registrationId, $this->registrationIds)) {
                array_push($this->registrationIds, $registrationId);
            }
        } else {
            throw new InvalidArgumentException("Invalid registration_id value");
        }

        return $this;
    }

    public function setNotificationAlert($alert) {
        if (!is_string($alert)) {
            throw new InvalidArgumentException("Invalid alert value");
        }
        $this->notificationAlert = $alert;
        return $this;
    }

    public function addWinPhoneNotification($alert=null, $title=null, $_open_page=null, $extras=null) {
        $winPhone = array();

        if (!is_null($alert)) {
            if (!is_string($alert)) {
                throw new InvalidArgumentException("Invalid winphone notification");
            }
            $winPhone['alert'] = $alert;
        }

        if (!is_null($title)) {
            if (!is_string($title)) {
                throw new InvalidArgumentException("Invalid winphone title notification");
            }
            if(strlen($title) > 0) {
                $winPhone['title'] = $title;
            }
        }

        if (!is_null($_open_page)) {
            if (!is_string($_open_page)) {
                throw new InvalidArgumentException("Invalid winphone _open_page notification");
            }
            if (strlen($_open_page) > 0) {
                $winPhone['_open_page'] = $_open_page;
            }
        }

        if (!is_null($extras)) {
            if (!is_array($extras)) {
                throw new InvalidArgumentException("Invalid winphone extras notification");
            }
            if (count($extras) > 0) {
                $winPhone['extras'] = $extras;
            }
        }

        if (count($winPhone) <= 0) {
            throw new InvalidArgumentException("Invalid winphone notification");
        }

        $this->winPhoneNotification = $winPhone;
        return $this;
    }

    public function setSmsMessage($content, $delay_time = 0) {
        $sms = array();
        if (is_string($content) && mb_strlen($content) < 480) {
            $sms['content'] = $content;
        } else {
            throw new InvalidArgumentException('Invalid sms content, sms content\'s length must in [0, 480]');
        }

        $sms['delay_time'] = ($delay_time === 0 || (is_int($delay_time) && $delay_time > 0 && $delay_time <= 86400)) ? $delay_time : 0;

        $this->smsMessage = $sms;
        return $this;
    }

    public function build() {
        $payload = array();

        // validate platform
        if (is_null($this->platform)) {
            throw new InvalidArgumentException("platform must be set");
        }
        $payload["platform"] = $this->platform;

        // validate audience
        $audience = array();
        if (!is_null($this->tags)) {
            $audience["tag"] = $this->tags;
        }
        if (!is_null($this->tagAnds)) {
            $audience["tag_and"] = $this->tagAnds;
        }
        if (!is_null($this->alias)) {
            $audience["alias"] = $this->alias;
        }
        if (!is_null($this->registrationIds)) {
            $audience["registration_id"] = $this->registrationIds;
        }

        if (is_null($this->audience) && count($audience) <= 0) {
            throw new InvalidArgumentException("audience must be set");
        } else if (!is_null($this->audience) && count($audience) > 0) {
            throw new InvalidArgumentException("you can't add tags/alias/registration_id/tag_and when audience='all'");
        } else if (is_null($this->audience)) {
            $payload["audience"] = $audience;
        } else {
            $payload["audience"] = $this->audience;
        }


        // validate notification
        $notification = array();

        if (!is_null($this->notificationAlert)) {
            $notification['alert'] = $this->notificationAlert;
        }

        if (!is_null($this->androidNotification)) {
            $notification['android'] = $this->androidNotification;
            if (is_null($this->androidNotification['alert'])) {
                if (is_null($this->notificationAlert)) {
                    throw new InvalidArgumentException("Android alert can not be null");
                } else {
                    $notification['android']['alert'] = $this->notificationAlert;
                }
            }
        }

        if (!is_null($this->iosNotification)) {
            $notification['ios'] = $this->iosNotification;
            if (is_null($this->iosNotification['alert'])) {
                if (is_null($this->notificationAlert)) {
                    throw new InvalidArgumentException("iOS alert can not be null");
                } else {
                    $notification['ios']['alert'] = $this->notificationAlert;
                }
            }
        }

        if (!is_null($this->winPhoneNotification)) {
            $notification['winphone'] = $this->winPhoneNotification;
            if (is_null($this->winPhoneNotification['alert'])) {
                if (is_null($this->winPhoneNotification)) {
                    throw new InvalidArgumentException("WinPhone alert can not be null");
                } else {
                    $notification['winphone']['alert'] = $this->notificationAlert;
                }
            }
        }

        if (count($notification) > 0) {
            $payload['notification'] = $notification;
        }

        if (count($this->message) > 0) {
            $payload['message'] = $this->message;
        }
        if (!array_key_exists('notification', $payload) && !array_key_exists('message', $payload)) {
            throw new InvalidArgumentException('notification and message can not all be null');
        }

        if (count($this->smsMessage)) {
            $payload['sms_message'] = $this->smsMessage;
        }

        if (count($this->options) <= 0) {
            $this->options(array('apns_production' => false));
        }

        $payload['options'] = $this->options;

        return $payload;
    }

    public function toJSON() {
        $payload = $this->build();
        return json_encode($payload);
    }

    public function printJSON() {
        echo $this->toJSON();
        return $this;
    }

    public function send() {
        $url = PushPayload::PUSH_URL;
        return Http::post($this->client, $url, $this->toJSON());
    }

    public function validate() {
        $url = PushPayload::PUSH_VALIDATE_URL;
        return Http::post($this->client, $url, $this->toJSON());
    }

    private function generateSendno() {
        return rand(100000, 4294967294);
    }

    # new methods
    public function iosNotification($alert = '', array $notification = array()) {
        # $required_keys = array('sound', 'badge', 'content-available', 'category', 'extras');
        $ios = array();
        $ios['alert'] = is_string($alert) ? $alert : '';
        if (!empty($notification)) {
            if (isset($notification['sound']) && is_string($notification['sound'])) {
                $ios['sound'] = $notification['sound'];
            }
            if (isset($notification['badge']) && (int)$notification['badge']) {
                $ios['badge'] = $notification['badge'];
            }
            if (isset($notification['content-available']) && is_bool($notification['content-available']) && $notification['content-available']) {
                $ios['content-available'] = $notification['content-available'];
            }
            if (isset($notification['category']) && is_string($notification['category'])) {
                $ios['category'] = $notification['category'];
            }
            if (isset($notification['extras']) && is_array($notification['extras']) && !empty($notification['extras'])) {
                $ios['extras'] = $notification['extras'];
            }
        }
        if (!isset($ios['sound'])) {
            $ios['sound'] = '';
        }
        if (!isset($ios['badge'])) {
            $ios['badge'] = '+1';
        }
        $this->iosNotification = $ios;
        return $this;
    }

    public function androidNotification($alert = '', array $notification = array()) {
        # $required_keys = array('title', 'build_id', 'extras');
        $android = array();
        $android['alert'] = is_string($alert) ? $alert : '';
        if (!empty($notification)) {
            if (isset($notification['title']) && is_string($notification['title'])) {
                $android['title'] = $notification['title'];
            }
            if (isset($notification['build_id']) && is_int($notification['build_id'])) {
                $android['build_id'] = $notification['build_id'];
            }
            if (isset($notification['extras']) && is_array($notification['extras']) && !empty($notification['extras'])) {
                $android['extras'] = $notification['extras'];
            }
        }
        $this->androidNotification = $android;
        return $this;
    }

    public function message($msg_content, array $msg = array()) {
        # $required_keys = array('title', 'content_type', 'extras');
        if (is_string($msg_content)) {
            $message = array();
            $message['msg_content'] = $msg_content;
            if (!empty($msg)) {
                if (isset($msg['title']) && is_string($msg['title'])) {
                    $message['title'] = $msg['title'];
                }
                if (isset($msg['content_type']) && is_string($msg['content_type'])) {
                    $message['content_type'] = $msg['content_type'];
                }
                if (isset($msg['extras']) && is_array($msg['extras']) && !empty($msg['extras'])) {
                    $message['extras'] = $msg['extras'];
                }
            }
            $this->message = $message;
        }
        return $this;
    }

    public function options(array $opts = array()) {
        # $required_keys = array('sendno', 'time_to_live', 'override_msg_id', 'apns_production', 'big_push_duration');
        if (!empty($opts)) {
            $options = array();
            if (isset($opts['sendno']) && is_int($opts['sendno'])) {
                $options['sendno'] = $opts['sendno'];
            }
            if (isset($opts['time_to_live']) && is_int($opts['time_to_live']) && $opts['time_to_live'] <= 864000 && $opts['time_to_live'] >= 0) {
                $options['time_to_live'] = $opts['time_to_live'];
            }
            if (isset($opts['override_msg_id']) && is_long($opts['override_msg_id'])) {
                $options['override_msg_id'] = $opts['override_msg_id'];
            }
            if (isset($opts['apns_production']) && is_bool($opts['apns_production'])) {
                $options['apns_production'] = $opts['apns_production'];
            } else {
                $options['apns_production'] = false;
            }
            if (isset($opts['big_push_duration']) && is_int($opts['big_push_duration']) && $opts['big_push_duration'] <= 1400 && $opts['big_push_duration'] >= 0) {
                $options['big_push_duration'] = $opts['big_push_duration'];
            }
            $this->options = $options;
        }
        return $this;
    }

    ###############################################################################
    ############# 以下函数已过期，不推荐使用，仅作为兼容接口存在 #########################
    ###############################################################################
    public function addIosNotification($alert=null, $sound=null, $badge=null, $content_available=null, $category=null, $extras=null) {
        $ios = array();

        if (!is_null($alert)) {
            if (!is_string($alert) && !is_array($alert)) {
                throw new InvalidArgumentException("Invalid ios alert value");
            }
            $ios['alert'] = $alert;
        }

        if (!is_null($sound)) {
            if (!is_string($sound)) {
                throw new InvalidArgumentException("Invalid ios sound value");
            }
            if ($sound !== Config::DISABLE_SOUND) {
                $ios['sound'] = $sound;
            }
        } else {
            // 默认sound为''
            $ios['sound'] = '';
        }

        if (!is_null($badge)) {
            if (is_string($badge) && !preg_match("/^[+-]{1}[0-9]{1,3}$/", $badge)) {
                if (!is_int($badge)) {
                    throw new InvalidArgumentException("Invalid ios badge value");
                }
            }
            if ($badge != Config::DISABLE_BADGE) {
                $ios['badge'] = $badge;
            }
        } else {
            // 默认badge为'+1'
            $ios['badge'] = '+1';
        }

        if (!is_null($content_available)) {
            if (!is_bool($content_available)) {
                throw new InvalidArgumentException("Invalid ios content-available value");
            }
            $ios['content-available'] = $content_available;
        }

        if (!is_null($category)) {
            if (!is_string($category)) {
                throw new InvalidArgumentException("Invalid ios category value");
            }
            if (strlen($category)) {
                $ios['category'] = $category;
            }
        }

        if (!is_null($extras)) {
            if (!is_array($extras)) {
                throw new InvalidArgumentException("Invalid ios extras value");
            }
            if (count($extras) > 0) {
                $ios['extras'] = $extras;
            }
        }

        if (count($ios) <= 0) {
            throw new InvalidArgumentException("Invalid iOS notification");
        }

        $this->iosNotification = $ios;
        return $this;
    }

    public function addAndroidNotification($alert=null, $title=null, $builderId=null, $extras=null) {
        $android = array();

        if (!is_null($alert)) {
            if (!is_string($alert)) {
                throw new InvalidArgumentException("Invalid android alert value");
            }
            $android['alert'] = $alert;
        }

        if (!is_null($title)) {
            if(!is_string($title)) {
                throw new InvalidArgumentException("Invalid android title value");
            }
            if(strlen($title) > 0) {
                $android['title'] = $title;
            }
        }

        if (!is_null($builderId)) {
            if (!is_int($builderId)) {
                throw new InvalidArgumentException("Invalid android builder_id value");
            }
            $android['builder_id'] = $builderId;
        }

        if (!is_null($extras)) {
            if (!is_array($extras)) {
                throw new InvalidArgumentException("Invalid android extras value");
            }
            if (count($extras) > 0) {
                $android['extras'] = $extras;
            }
        }

        if (count($android) <= 0) {
            throw new InvalidArgumentException("Invalid android notification");
        }

        $this->androidNotification = $android;
        return $this;
    }

    public function setMessage($msg_content, $title=null, $content_type=null, $extras=null) {
        $message = array();

        if (is_null($msg_content) || !is_string($msg_content)) {
            throw new InvalidArgumentException("Invalid message content");
        } else {
            $message['msg_content'] = $msg_content;
        }

        if (!is_null($title)) {
            if (!is_string($title)) {
                throw new InvalidArgumentException("Invalid message title");
            }
            $message['title'] = $title;
        }

        if (!is_null($content_type)) {
            if (!is_string($content_type)) {
                throw new InvalidArgumentException("Invalid message content type");
            }
            $message["content_type"] = $content_type;
        }

        if (!is_null($extras)) {
            if (!is_array($extras)) {
                throw new InvalidArgumentException("Invalid message extras");
            }
            if (count($extras) > 0) {
                $message['extras'] = $extras;
            }
        }

        $this->message = $message;
        return $this;
    }

    public function setOptions($sendno=null, $time_to_live=null, $override_msg_id=null, $apns_production=null, $big_push_duration=null) {
        $options = array();

        if (!is_null($sendno)) {
            if (!is_int($sendno)) {
                throw new InvalidArgumentException('Invalid option sendno');
            }
            $options['sendno'] = $sendno;
        } else {
            $options['sendno'] = $this->generateSendno();
        }

        if (!is_null($time_to_live)) {
            if (!is_int($time_to_live) || $time_to_live < 0 || $time_to_live > 864000) {
                throw new InvalidArgumentException('Invalid option time to live, it must be a int and in [0, 864000]');
            }
            $options['time_to_live'] = $time_to_live;
        }

        if (!is_null($override_msg_id)) {
            if (!is_long($override_msg_id)) {
                throw new InvalidArgumentException('Invalid option override msg id');
            }
            $options['override_msg_id'] = $override_msg_id;
        }

        if (!is_null($apns_production)) {
            if (!is_bool($apns_production)) {
                throw new InvalidArgumentException('Invalid option apns production');
            }
            $options['apns_production'] = $apns_production;
        } else {
            $options['apns_production'] = false;
        }

        if (!is_null($big_push_duration)) {
            if (!is_int($big_push_duration) || $big_push_duration < 0 || $big_push_duration > 1440) {
                throw new InvalidArgumentException('Invalid option big push duration, it must be a int and in [0, 1440]');
            }
            $options['big_push_duration'] = $big_push_duration;
        }

        $this->options = $options;
        return $this;
    }
}
 

final class Http {

    private static $LIMIT_KEYS = array('X-Rate-Limit-Limit'=>'rateLimitLimit', 'X-Rate-Limit-Remaining'=>'rateLimitRemaining', 'X-Rate-Limit-Reset'=>'rateLimitReset');

    public static function get($client, $url) {
        $response = self::sendRequest($client, $url, Config::HTTP_GET, $body=null);
        return self::processResp($response);
    }
    public static function post($client, $url, $body) {
        $response = self::sendRequest($client, $url, _Config::HTTP_POST, $body);
        return self::processResp($response);
    }
    public static function put($client, $url, $body) {
        $response = self::sendRequest($client, $url, Config::HTTP_PUT, $body);
        return self::processResp($response);
    }
    public static function delete($client, $url) {
        $response = self::sendRequest($client, $url, Config::HTTP_DELETE, $body=null);
        return self::processResp($response);
    }

    private static function sendRequest($client, $url, $method, $body=null, $times=1) {
        self::log($client, "Send " . $method . " " . $url . ", body:" . $body . ", times:" . $times);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, _Config::USER_AGENT);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, _Config::CONNECT_TIMEOUT);  // 连接建立最长耗时
        curl_setopt($ch, CURLOPT_TIMEOUT, _Config::READ_TIMEOUT);  // 请求最长耗时
        // 设置SSL版本 1=CURL_SSLVERSION_TLSv1, 不指定使用默认值,curl会自动获取需要使用的CURL版本
        // curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // 如果报证书相关失败,可以考虑取消注释掉该行,强制指定证书版本
        //curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
        // 设置Basic认证
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $client->getAuthStr());
        // 设置Post参数
        if ($method === _Config::HTTP_POST) {
            curl_setopt($ch, CURLOPT_POST, true);
        } else if ($method === _Config::HTTP_DELETE || $method === _Config::HTTP_PUT) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        if (!is_null($body)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Connection: Keep-Alive'
        ));

        $output = curl_exec($ch);
        $response = array();
        $errorCode = curl_errno($ch);
        if ($errorCode) {
            $retries = $client->getRetryTimes();
            if ($times < $retries) {
                return self::sendRequest($client, $url, $method, $body, ++$times);
            } else {
                if ($errorCode === 28) {
                    throw new APIConnectionException("Response timeout. Your request has probably be received by JPush Server,please check that whether need to be pushed again.");
                } elseif ($errorCode === 56) {
                // resolve error[56 Problem (2) in the Chunked-Encoded data]
                    throw new APIConnectionException("Response timeout, maybe cause by old CURL version. Your request has probably be received by JPush Server, please check that whether need to be pushed again.");
                } else {
                    throw new APIConnectionException("Connect timeout. Please retry later. Error:" . $errorCode . " " . curl_error($ch));
                }
            }
        } else {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header_text = substr($output, 0, $header_size);
            $body = substr($output, $header_size);
            $headers = array();
            foreach (explode("\r\n", $header_text) as $i => $line) {
                if (!empty($line)) {
                    if ($i === 0) {
                        $headers['http_code'] = $line;
                    } else if (strpos($line, ": ")) {
                        list ($key, $value) = explode(': ', $line);
                        $headers[$key] = $value;
                    }
                }
            }
            $response['headers'] = $headers;
            $response['body'] = $body;
            $response['http_code'] = $httpCode;
        }
        curl_close($ch);
        return $response;
    }

    public static function processResp($response) {
        if($response['http_code'] === 200) {
            $result = array();
            $data = json_decode($response['body'], true);
            if (!is_null($data)) {
                $result['body'] = $data;
            }
            $result['http_code'] = $response['http_code'];
            $headers = $response['headers'];
            if (is_array($headers)) {
                $limit = array();
                foreach (self::$LIMIT_KEYS as $key => $value) {
                    if (array_key_exists($key, $headers)) {
                        $limit[$value] = $headers[$key];
                    }
                }
                if (count($limit) > 0) {
                    $result['headers'] = $limit;
                }
                return $result;
            }
            return $result;
        } else {
            throw new APIRequestException($response);
        }
    }

    public static function log($client, $content) {
        if (!is_null($client->getLogFile())) {
            error_log($content . "\r\n", 3, $client->getLogFile());
        }
    }
}
class APIRequestException extends JPushException {
    private $http_code;
    private $headers;

    private static $expected_keys = array('code', 'message');

    function __construct($response){
        $this->http_code = $response['http_code'];
        $this->headers = $response['headers'];

        $body = json_decode($response['body'], true);

        if (key_exists('error', $body)) {
            $this->code = $body['error']['code'];
            $this->message = $body['error']['message'];
        } else {
            $this->code = $body['code'];
            $this->message = $body['message'];
        }
    }

    public function __toString() {
        return "\n" . __CLASS__ . " -- [{$this->code}]: {$this->message} \n";
    }

    public function getHttpCode() {
        return $this->http_code;
    }
    public function getHeaders() {
        return $this->headers;
    }

}

class JPushException extends \Exception {

    function __construct($message) {
        parent::__construct($message);
    }
}
class APIConnectionException extends JPushException {

    function __toString() {
        return "\n" . __CLASS__ . " -- {$message} \n";
    }
}

 