<?php
ini_set('max_execution_time', 0);
include_once 'helper.php';

// command: php crawl.php
// agruments:
//  lang=vi channel=1 book_id=1234 'search=ma đế'
foreach ($argv as $arg) {
    $e = explode("=", $arg);
    if (count($e) == 2)
        $_GET[$e[0]] = $e[1];
    else
        $_GET[$e[0]] = 0;
}
$languages = ['vi', 'id', 'th', 'ru', 'en'];
$channels = [1, 2];

$book_list_data = [];

foreach ($languages as $language) {
    if (isset($_GET['lang']) && $language != $_GET['lang']) {
        continue;
    }

    echo 'Crawl hinovel language: ' . $language . "\n";
    $headers = build_headers($language);
    foreach ($channels as $channel) {
        if (isset($_GET['channel']) && $channel != $_GET['channel']) {
            continue;
        }
        // get danh sach truyen
        $page = 1;
        while (true) {
            $fields = [
                'channel' => $channel,
                'page' => $page
            ];
            $response = scurl('api/book/sellTop', $fields, $headers);
            $array_response = json_decode($response, true);
            if (empty($array_response['data'])) {
                break;
            }

            foreach ($array_response['data'] as $book) {
                $book_id = $book['book_id'];
                $book_name = $book['book_name'];
                $fields = ['book_id' => $book_id];

                if (isset($_GET['book_id']) && $book_id != $_GET['book_id']) {
                    continue;
                }
                if (isset($_GET['search']) && strpos(mb_strtolower($book_name, 'UTF-8'), mb_strtolower($_GET['search'], 'UTF-8')) === false) {
                    continue;
                }

                $prefix_log = '[LANG ' . strtolower($language) . '][CHANNEL ' . $channel . '][PAGE ' . $page . '][BOOK_ID ' . $book_id . ']';
                $book_res = scurl('api/book/detail', $fields, $headers);
                $book_arr = json_decode($book_res, true);
                $store_path = '../storage/' . implode('/', [$language, $book_id]);
                $section_path = $store_path . '/sections';
                if (!file_exists($store_path)) {
                    mkdir($section_path, 0777, true);
                }

                echo $prefix_log . ' Store book detail: ' . $book_name . "\n";

                $book_list_data[$book_id] = [
                    'book_id' => $book_id,
                    'title' => $book_name,
                    'image' => $book_arr['data']['book_pic'],
                    'author' => $book_arr['data']['writer_name'],
                    'category' => $book_arr['data']['category_name'],
                    'label' => $book_arr['data']['label_name'],
                    'score' => $book_arr['data']['score'],
                    'section_update_time' => date('H:i d/m/Y', $book_arr['data']['section_update_time']),
                    'section' => []
                ];

                file_put_contents($store_path . '/detail.json', json_encode($book_arr));
                if (!file_exists($store_path . '/sections.json')) {
                    file_put_contents($store_path . '/sections.json', json_encode([]));
                }

                // get section list
                $fields = ['book_id' => $book_id];

                echo $prefix_log . ' Crawl book section: ' . $book_name . "\n";
                $section_res = scurl('api/book/sectionList', $fields, $headers);
                $section_arr = json_decode($section_res, true);
                if (empty($section_arr['data'])) {
                    echo $prefix_log . ' Book section empty: ' . $book_name . "\n";
                    continue;
                }
                $section_list = file_get_contents($store_path . '/sections.json');
                $section_list = []; // json_decode($section_list, true);
                $new_chap_update_msg = file_get_contents('updated.txt');
                foreach ($section_arr['data'] as $section) {
                    $section_id = $section['section_id'];
                    $section_title = $section['title'];

                    $book_list_data[$book_id]['section'] = [
                        'section_id' => $section_id,
                        'title' => $section_title
                    ];

                    if (empty($section['osspath'])) {
                        echo "-----------------\n";
                        echo $prefix_log . ' Book section empty: ' . $book_name . " => $section_id\n";
                        echo "-----------------\n";
                        continue;
                    }

                    $section_list[] = [
                        'section_id' => $section_id,
                        'title' => $section_title
                    ];

                    // if (!file_exists($section_path . '/' . $section_id)) {
                    // save section and encrypt content
                    if (!file_exists($section_path . '/' . $section_id)) {
                        echo $prefix_log . ' Store book section [' . $section_id . ']: ' . $section_title . "\n";
                        mkdir($section_path . '/' . $section_id, 0777, true);
                        $encrypt_content = file_get_contents($section['osspath']);
                        file_put_contents($section_path . '/' . $section_id . '/encrypt_content.txt', $encrypt_content);
                        file_put_contents($section_path . '/' . $section_id . '/encrypt_key.txt', $section['amd5']);
                        file_put_contents($section_path . '/' . $section_id . '/detail.json', json_encode($section));
                    }

                    if (file_exists($section_path . '/' . $section_id . '/content.txt')) {
                        continue;
                    }

                    echo $prefix_log . ' Decrypt [' . $section_id . ']: ' . $section_title . "\n";
                    $content = decrypt_section($section_path . '/' . $section_id);
                    $section_list[] = [
                        'section_id' => $section_id,
                        'title' => $section_title
                    ];
                    file_put_contents($section_path . '/' . $section_id . '/content.txt', $content);
                    unlink($section_path . '/' . $section_id . '/encrypt_content.txt');
                    unlink($section_path . '/' . $section_id . '/encrypt_key.txt');

                    $new_chap_update_msg = "----------------\nTruyện *" . $book_name . "* Cập nhật!\n" . $section_title . "\nLink: https://anynovel.net/read/$book_id/$section_id";

                    // TODO: send notify to user
//                     } else {
// //                        echo 'Existed book section: ' . $section_title . " => $section_path/$section_id\n";
//                     }
                }
                file_put_contents('updated.txt', $new_chap_update_msg);


                file_put_contents($store_path . '/sections.json', json_encode($section_list));
            }
            $page++;
        }
    }
    file_put_contents('../storage/' . $language . '/book_list.json', json_encode($book_list_data));
}

function scurl($path, $fields = false, $request_headers = [])
{
    $target = 'http://api.hinovelasia.com/' . $path;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $target);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($fields) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_REFERER, $target);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function build_headers($language)
{
    $headers = array();
    $headers[] = 'Connection: keep-alive';
    $headers[] = 'Pragma: no-cache';
    $headers[] = 'Cache-Control: no-cache';
    $headers[] = 'Apptype: android';
    $headers[] = 'Osuuid: 00000000-3771-0f25-3771-0f2500000000';
    $headers[] = 'Osversion: 9';
    $headers[] = 'Usertoken: 32fa232cdf7a6ff1f3eb0e490a6e477887ebcb3c6b6c4b562a76cfc24e48a16b';
    $headers[] = 'X-Requested-With: XMLHttpRequest';
    $headers[] = 'Ostype: 1';
    $headers[] = 'Phonetype: SM-G955N';
    $headers[] = 'Sign: 8e9ab4e607a3757dc4ad4853dd464123';
    $headers[] = 'Lang: ' . $language;
    $headers[] = 'Phonebrand: samsung';
    $headers[] = 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36';
    $headers[] = 'Utc: 7';
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    $headers[] = 'Accept: */*';
    $headers[] = 'Phoneosversion: 9';
    $headers[] = 'Timestamp: 1604975938';
    $headers[] = 'Appversion: 3.0.10';
    $headers[] = 'Accept-Language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,fr-FR;q=0.6,fr;q=0.5';
    return $headers;
}
