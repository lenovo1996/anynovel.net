<?php

function _get($file, $array = true)
{
    if (!file_exists($file)) {
        return $array ? [] : '';
    }

    $content = file_get_contents($file);
    if (empty($content)) {
        return $array ? [] : '';
    }

    if ($array) {
        return json_decode($content, true);
    } else {
        return $content;
    }
}


function _scandir($path)
{
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
    $data = array_values(array_diff(scandir($path), array('.', '..')));
    sort($data);
    return $data;
}


function communicate($data)
{
    $endpoint = 'https://hooks.slack.com/services/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';
    //Initialize CURL, setup, and send data.
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    //Return response from Slack.
    return $result;
}

function send_log($message)
{
    $channel = ['hinovel'];
    $icon = ':peach:';
    $username = 'Đệ Của Phi';

    /**
     * If channel is an array, it means we are sending to multiple
     * channels. In this case, we need to run a foreach loop.
     ***/
    if (is_array($channel) && count($channel) >= 1) {
        foreach ($channel as $key => $c) {
            $data = 'payload=' . json_encode(
                    array(
                        "channel" => "#{$c}",
                        "username" => $username,
                        "text" => $message,
                        "icon_emoji" => $icon
                    )
                );
            $output = communicate($data);
        }
        return true;
        /**
         * If channel is not an array, but is also not empty, this means
         * we are sending to only one channel.
         **/
    } elseif (!is_array($channel) && !empty($channel)) {
        $data = 'payload=' . json_encode(
                array(
                    "channel" => "#" . $channel,
                    "username" => $username,
                    "text" => $message,
                    "icon_emoji" => $icon
                )
            );
        $output = communicate($data);
        return true;
    }
}


function book_filter($detail)
{
    $detail = $detail['data'] ?? [];

    $keep = [
        'book_id',
        'writer_name',
        'word_num',
        'book_pic',
        'book_desc',
        'category_name',
        'label_name',
        'book_name',
        'book_name'
    ];

    foreach ($detail as $key => $value) {
        if (!in_array($key, $keep)) {
            unset($detail[$key]);
        }
    }

    return $detail;
}

function get_sections($section_list_path)
{
    $section_list = _scandir($section_list_path);
    $section_data = [];
    foreach ($section_list as $section) {
        $section_path = $section_list_path . '/' . $section;
        $section_detail = _get($section_path . '/detail.json');
        $section_detail = section_filter($section_detail);
        $section_data[] = $section_detail;
    }
    return $section_data;
}

function get_sections_post($section_list_path, $chapter = false)
{
    $section_list = _scandir($section_list_path);
    $section_content = [];
    foreach ($section_list as $section) {
        if ($chapter && $section < $chapter) {
            continue;
        }
        $section_path = $section_list_path . '/' . $section;
        $section_content[$section] = decrypt_section($section_path);
    }
    return $section_content;
}

function section_filter($section_detail)
{
    $keep = [
        'section_id',
        'title',
        'list_order',
        'book_id'
    ];

    foreach ($section_detail as $key => $value) {
        if (!in_array($key, $keep)) {
            unset($section_detail[$key]);
        }
    }

    return $section_detail;
}

function decrypt_section($section_path)
{
    exec('java hinovel_decrypt ' . realpath($section_path), $output);
    return implode("<br />", $output);
}

function convert_hashtag($hashtag)
{
    $ha = [];
    foreach ($hashtag as $h) {
        $ha[] = '#' . str_replace(' ', '_', $h);
    }
    foreach ($hashtag as $h) {
        $ha[] = '#' . str_replace(' ', '_', replace_str($h));
    }
    return implode(', ', $ha);
}

function replace_str($str)
{
    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
    $str = preg_replace("/(đ)/", 'd', $str);

    $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
    $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
    $str = preg_replace("/(Đ)/", 'D', $str);
    return $str;
}