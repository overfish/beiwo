<?php

if (!function_exists('filter_emoji')) {
    /**
     * 过滤表情和特殊字符
     * 注意：这个方法会过滤掉一些语言，比如 维吾尔语
     *
     * @param $str
     * @return string|string[]|null
     */
    function filter_emoji($str)
    {
        $str = preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);

        return $str;
    }
}

if (!function_exists('text_to_html')) {
    /**
     * 将文本转换成html段落
     *
     * @param $text
     * @return string
     */
    function text_to_html($text)
    {
        $text = mb_ereg_replace("\n", "</p><p>", $text);
        $text = mb_ereg_replace("\t", "", $text);

        $text = "<p>" . $text . '</p>';
        return $text;
    }
}

if (!function_exists('get')) {

    /**
     * simple get query
     *
     * @param string $url
     * @param array $params
     * @return bool|string
     */
    function get($url, $params)
    {
        $curl = curl_init();
        if (stripos($url,'https://') !== false) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }

        $get = [];
        foreach ($params as $field => $val) {
            $get[] = $field . '=' . urlencode($val);
        }
        $url = trim($url, '?');
        $query = $url . '?' . implode('&', $get);

        curl_setopt($curl, CURLOPT_URL, $query);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1 );
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        if (intval($info["http_code"]) == 200) {
            return $response;
        }

        return false;
    }
}


if (!function_exists('post')) {
    /**
     * simple post query
     *
     * @param string $url
     * @param array $params
     * @param int $timeout
     * @param array $header
     * @param string $cookie
     * @return bool|string
     */
    function post($url, $params, $timeout = 0, $header = [], $cookie='')
    {
        $curl = curl_init();
        if (stripos($url, 'https://') !== false) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }

        $post = [];
        foreach ($params as $filed => $val) {
            $post[] = $filed . '=' . $val;
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1 );

        $timeout && curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);

        curl_setopt($curl, CURLOPT_POST,true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, implode("&", $post));

        $header && curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        $cookie && curl_setopt($curl, CURLOPT_COOKIE, $cookie);

        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        if (intval($info["http_code"]) == 200) {
            return $response;
        }

        return false;
    }
}

if (!function_exists('format_number')) {
    /**
     * 将数字按单位格式化 默认万
     * @param $num
     * @param int $decimals 精度
     * @param int $unit 单位, 0-个 1-十 2-百 3-千 4-万 5-十万 6-百万 7-千万 8-亿 9-十亿
     * @return string
     */
    function format_number($num, $decimals = 2, $unit = 4)
    {
        $mod = pow(10, $unit);

        // 单位
        $unitText = ['个', '十', '百', '千', '万', '十万', '百万', '千万', '亿', '十亿', '百亿', '千亿', '万亿', '亿亿'];

        if ($num < $mod) {
            return $num;
        }

        return round($num/$mod, $decimals). $unitText[$unit];
    }
}

if (!function_exists('make_order_no')) {
    /**
     * 随机订单号
     *
     * @param $id
     * @return string
     */
    function make_order_no($id = 0)
    {
        return date('YmdHis') . mt_rand(100000, 999999) . str_pad($id, 10, 0, STR_PAD_LEFT);
    }
}

if (!function_exists('time_for_diff')) {
    /**
     * 比较时间差
     *
     * @param int $theTime 需要比较的时间 时间戳或日期
     * @param int $nowTime 参考时间 不传时取当前时间戳
     * @return bool|string
     */
    function time_for_diff($theTime, $nowTime = null) {

        $theTime = strtotime($theTime) ?: $theTime;
        if ($nowTime == null) {
            $nowTime = time();
        } else {
            $nowTime = strtotime($nowTime) ?: $nowTime;
        }

        $dur = $nowTime - $theTime;

        if ($dur <= 0) {
            return '1秒';
        } else if ($dur <= 60) {
            return $dur . '秒';
        } else if ($dur <= 86400) {
            return ceil($dur/3600) . '小时';
        } else if ($dur <= 2592000) {
            return ceil($dur/86400) . '天';
        } else if ($dur <= 31104000) {
            return ceil($dur/2592000). '月';
        } else {
            return ceil($dur/31104000) . '年';
        }
    }
}

if (!function_exists('is_indexed_array')) {
    /**
     * 是否索引数组 索引数组返回 true
     *
     * @param $array
     * @return bool
     */
    function is_indexed_array($array) {
        if (is_array($array)) {
            $keys = array_keys($array);
            return $keys == array_keys($keys);
        }
        return false;
    }
}

if (!function_exists('rand_str')) {
    /**
     * 随机字符串
     *
     * @return string
     */
    function rand_str()
    {
        return md5(date('YmdHis') . mt_rand(1000, 9999));
    }
}

if (!function_exists('parse_file_base')) {
    /**
     * 解析文件base数据流，返回后缀名和文件流
     *
     * @param $baseCode
     * @return array
     */
    function parse_file_base($baseCode)
    {
        $data = explode(',', $baseCode);

        $extension_str = reset($data);
        if (preg_match('/.*?\/(.*?);/', $extension_str, $match)) {
            $extension = $match[1];
        }
        return [
            'extension' => $extension ?? '',
            'file_base' => end($data)
        ];
    }
}
