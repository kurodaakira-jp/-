<?php
/**
 * Created by PhpStorm.
 * User: akira
 * Date: 2018-12-30
 * Time: 13:32
 */
header("Content-type:text/html;charset=utf-8");
if (preg_match("/Android|iPhone|IOS/i", $_SERVER['HTTP_USER_AGENT'])) die('<p style="color:#fff;background:#ca0000;margin: 5px;padding: 5px;text-align: center;">禁止在移动设备使用该脚本</p>');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>网易云音乐 - 歌词批量生成脚本</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            outline: none;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }
        form {
            margin-bottom: 8px;
            padding: 0 5px 8px 5px;
            border-bottom: 2px solid #999;
        }
        fieldset {
            margin: 10px 0;
            padding: 5px;
        }
        input[type="text"] {
            height: 32px;
            border: 1px solid #999;
            text-indent: 0.5em;
            border-radius: 4px;
        }
        .api input[type="text"], .path input[type="text"], .opencc input[type="text"] {
            width: 100%;
        }
        .style input[type="radio"], .trans input[type="radio"], .overwrite input[type="radio"], .opencc input[type="radio"], .info input[type="radio"] {
            vertical-align: top;
            margin-right: 5px;
            margin-top: 4px;
        }
        .precision input[type="text"]{
            width: 33.05%;
        }
        .submit {
            color: #fff;
            width: 120px;
            height: 40px;
            background: #000;
            font-size: 16px;
            cursor: pointer;
            border: none;
            -webkit-border-radius: 6px;
            -moz-border-radius: 6px;
            border-radius: 6px;
        }
        .tips {
            display: none;
            color: #fff;
            width: 300px;
            height: 125px;
            background: #000;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            line-height: 125px;
            -webkit-border-radius: 8px;
            -moz-border-radius: 8px;
            border-radius: 8px;
            position: fixed;
            top: 35%;
            left: 50%;
            margin-left: -150px;
        }
        .top {
            color: #fff;
            width: 70px;
            height: 70px;
            background: #000;
            position: fixed;
            right: 10px;
            bottom: 10px;
            text-align: center;
            line-height: 70px;
            font-weight: bold;
            text-decoration: none;
            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
            border-radius: 50%;
        }
    </style>
    <body>
        <form action="" method="post">
            <fieldset class="api">
                <legend>网易云音乐 API 地址</legend>
                <input type="text" name="api" placeholder="需要带 http:// 或 https:// 前缀" value="<?php echo isset($_POST['api']) ? $_POST['api'] : '' ?>">
            </fieldset>
            <fieldset class="opencc">
                <legend>OpenCC 设置</legend>
                <input type="radio" name="opencc" value="true" <?php if(!isset($_POST['opencc']) || $_POST['opencc']) echo 'checked="checked"'; ?>>启用
                <input type="radio" name="opencc" value="" <?php if(isset($_POST['opencc']) && !$_POST['opencc']) echo 'checked="checked"'; ?>>不启用
                <input type="text" name="s2t" style="margin: 5px 0;" placeholder="简转繁：如果为 Linux、Unix 系统则无需输入路径，默认为：s2t.json          如果为 windows 系统则需手动指定 OpenCC json 的绝对路径，例如：D:/opencc/s2t.json" value="<?php echo isset($_POST['s2t']) ? $_POST['s2t'] : ''; ?>">
                <input type="text" name="t2s" placeholder="繁转简：如果为 Linux、Unix 系统则无需输入路径，默认为：t2s.json          如果为 windows 系统则需手动指定 OpenCC json 的绝对路径，例如：D:/opencc/t2s.json" value="<?php echo isset($_POST['t2s']) ? $_POST['t2s'] : ''; ?>">
            </fieldset>
            <fieldset class="path">
                <legend>音乐文件夹绝对路径</legend>
                <input type="text" name="path" placeholder="例如：D:/music" value="<?php echo isset($_POST['path']) ? $_POST['path'] : '' ?>">
            </fieldset>
            <fieldset class="precision">
                <legend>匹配精度</legend>
                <input type="text" name="name_precision" maxlength="3" placeholder="歌曲名字，默认：80（0 ~ 100，单位为%）">
                <input type="text" name="artist_precision" maxlength="3" placeholder="艺术家名字，默认：80（0 ~ 100，单位为%）">
                <input type="text" name="duration_precision" maxlength="2" placeholder="歌曲时长偏移量，默认：正负5（0 ~ 30，单位为s）">
            </fieldset>
            <fieldset class="style">
                <legend>歌词样式</legend>
                <input type="radio" name="style" value="1" <?php if(!isset($_POST['style']) || $_POST['style'] === '1') echo 'checked="checked"'; ?>>样式一
                <input type="radio" name="style" value="2" <?php if(isset($_POST['style']) && $_POST['style'] === '2') echo 'checked="checked"'; ?>>样式二
            </fieldset>
            <fieldset class="trans">
                <legend>是否选择翻译</legend>
                <input type="radio" name="trans" value="true" <?php if(!isset($_POST['trans']) || $_POST['trans']) echo 'checked="checked"'; ?>>是
                <input type="radio" name="trans" value="" <?php if(isset($_POST['trans']) && !$_POST['trans']) echo 'checked="checked"'; ?>>否
            </fieldset>
            <fieldset class="overwrite">
                <legend>是否覆盖已有歌词</legend>
                <input type="radio" name="overwrite" value="true">是
                <input type="radio" name="overwrite" value="" checked="checked">否
            </fieldset>
            <fieldset class="info">
                <legend>显示详细的匹配信息</legend>
                <input type="radio" name="info" value="true">开启
                <input type="radio" name="info" value="" checked="checked">关闭
            </fieldset>
            <input type="submit" name="make" value="生成 LRC 歌词" class="submit">
            <script>
                window.onload = function () {
                    var submit = document.getElementsByClassName('submit')[0];
                    var tips = document.getElementsByClassName('tips')[0];
                    var flag = true;
                    var count = 0;
                    submit.onclick = function () {
                        if (flag) {
                            setTimeout(function () {
                                tips.style.display = 'block';
                                tips.innerHTML = '歌词匹配生成中';
                                setInterval(function () {
                                    count++;
                                    if (count == 1) {
                                        tips.innerHTML = '歌词匹配生成中 .';
                                    } else if (count == 2) {
                                        tips.innerHTML = '歌词匹配生成中 . .';
                                    } else if (count == 3) {
                                        tips.innerHTML = '歌词匹配生成中 . . .';
                                    } else if (count == 4) {
                                        tips.innerHTML = '歌词匹配生成中';
                                        count = 0;
                                    }
                                }, 1000)
                            }, 500)
                        }
                        flag = false;
                    }
                }
            </script>
        </form>
        <div class="tips"></div>
        <a href="#" class="top">回顶部</a>
    </body>
</head>
<?php
if (!isset($_POST['make'])) die();
if ($_POST['api'] == null) die('<p style="color:#fff;background:#ca0000;margin: 5px;padding: 5px;text-align: center;">API 地址不能为空</p>');
if ($_POST['opencc'] && ($_POST['s2t'] == null || $_POST['t2s'] == null) && preg_match('/windows/i',$_SERVER['HTTP_USER_AGENT'])) die('<p style="color:#fff;background:#ca0000;margin: 5px;padding: 5px;text-align: center;">OpenCC 路径不能为空</p>');
if ($_POST['path'] == null) die('<p style="color:#fff;background:#ca0000;margin: 5px;padding: 5px;text-align: center;">音乐文件夹路径不能为空</p>');
if ($_POST['name_precision'] != null) if (!is_numeric($_POST['name_precision']) || $_POST['name_precision'] > 100 || $_POST['name_precision'] < 0) die('<p style="color:#fff;background:#ca0000;margin: 5px;padding: 5px;text-align: center;">歌曲名精度，参数错误</p>');
if ($_POST['artist_precision'] != null) if (!is_numeric($_POST['artist_precision']) || $_POST['artist_precision'] > 100 || $_POST['artist_precision'] < 0) die('<p style="color:#fff;background:#ca0000;margin: 5px;padding: 5px;text-align: center;">艺术家名精度，参数错误</p>');
if ($_POST['duration_precision'] != null) if (!is_numeric($_POST['duration_precision']) || $_POST['duration_precision'] > 30 || $_POST['duration_precision'] < 0) die('<p style="color:#fff;background:#ca0000;margin: 5px;padding: 5px;text-align: center;">歌曲时长偏移，参数错误</p>');

// api、音乐文件夹路径末端判断是否有斜杠
$api = mb_substr(trim($_POST['api']), -1) === '/' ? trim($_POST['api']) : trim($_POST['api']).'/';
$path = mb_substr(trim($_POST['path']), -1) === '/' ? trim($_POST['path']) : trim($_POST['path']).'/';

// 根据 UA 判断设备类型
if (preg_match("/mac/i", $_SERVER['HTTP_USER_AGENT'])) {
    // 音乐文件夹路径格式化
    $path = str_replace("\\","",$path);
    // OpenCC 路径格式化
    if ($_POST['opencc']) {
        $oc_s2t_path = $_POST['s2t'] != null ? trim($_POST['s2t']) : 's2t.json';
        $oc_s2t_path = str_replace("\\","",$oc_s2t_path);
        $oc_t2s_path = $_POST['t2s'] != null ? trim($_POST['t2s']) : 't2s.json';
        $oc_t2s_path = str_replace("\\","",$oc_t2s_path);
    } else {
        $oc_s2t_path = '';
        $oc_t2s_path = '';
    }
} else if (preg_match('/windows/i',$_SERVER['HTTP_USER_AGENT'])) {
    // 音乐文件夹路径格式化
    $path = str_replace("\\","/",$path);
    // OpenCC 路径格式化
    if ($_POST['opencc']) {
        $oc_s2t_path = $_POST['s2t'] != null ? trim($_POST['s2t']) : 's2t.json';
        $oc_s2t_path = str_replace("\\","/",$oc_s2t_path);
        $oc_t2s_path = $_POST['t2s'] != null ? trim($_POST['t2s']) : 't2s.json';
        $oc_t2s_path = str_replace("\\","/",$oc_t2s_path);
    } else {
        $oc_s2t_path = null;
        $oc_t2s_path = null;
    }
}

// API URL
$api = mb_substr(trim($_POST['api']), -1) == '/' ? mb_substr(trim($_POST['api']), 0, -1) : trim($_POST['api']);

// 扫描目录
if (!$dir = scandir($path)) die('<p style="color:#fff;background:#ca0000;margin: 5px;padding: 5px;text-align: center;">目录打开失败</p>');

// 调用 getid3 获取歌曲信息
require_once('getid3/getid3.php');
$getID3 = new getID3();

// 调用 OpenCC 进行繁体转简体
if ($_POST['opencc']) {
    if (!$oc_s2t = opencc_open($oc_s2t_path)) die('<p style="color:#fff;background:#ca0000;margin: 5px;padding: 5px;text-align: center;">OpenCC(s2t.json) 调用异常</p>');
    if (!$oc_t2s = opencc_open($oc_t2s_path)) die('<p style="color:#fff;background:#ca0000;margin: 5px;padding: 5px;text-align: center;">OpenCC(t2s.json) 调用异常</p>');
} else {
    $oc_s2t = null;
    $oc_t2s = null;
}

// 精度
$name_precision = $_POST['name_precision'] == null ? 80 : (int)$_POST['name_precision'];
$artist_precision = $_POST['artist_precision'] == null ? 80 : (int)$_POST['artist_precision'];
$duration_precision = $_POST['duration_precision'] == null ? 5 : ((int)$_POST['duration_precision']);

// 开始运行！！！
for($i = 0;$i < count($dir);$i++) {
    // 文件不为音乐时跳过
    if (!preg_match("/flac|wav|mp3/i", substr($dir[$i], -4))) continue;
    // 当有歌词文件时跳过
    if (in_array(preg_split("/.(?=[^.]*$)/", $dir[$i])[0] . '.lrc', $dir) && !$_POST['overwrite']) continue;
    app($dir[$i], $getID3, $path, $oc_s2t, $oc_t2s, $api, $name_precision, $artist_precision, $duration_precision);
}

// OpenCC 调用结束，关闭
if ($_POST['opencc']) {
    opencc_close($oc_s2t);
    opencc_close($oc_t2s);
}

/**
 * @param $file_name 音乐文件名
 * @param $getID3 getID3
 * @param $path 歌曲路径
 * @param $oc_s2t OpenCC 简转繁
 * @param $oc_t2s OpenCC 繁转简
 * @param $api API URL
 * @param $name_precision 歌曲名精度
 * @param $artist_precision 艺术家名精度
 * @param $duration_precision 歌曲时长精度
 */
function app($file_name, $getID3, $path, $oc_s2t, $oc_t2s, $api, $name_precision, $artist_precision, $duration_precision) {
    $FileInfo = $getID3->analyze($path . $file_name);
    $duration = floor($FileInfo['playtime_seconds']);
    $type = $FileInfo['audio']['dataformat'];
    $file_path = $FileInfo['filepath'];
    $file_name = $FileInfo['filename'];
    if (isset($FileInfo['tags'])) {
        if ($type == 'flac') {
            $music_name = isset($FileInfo['tags']['vorbiscomment']['title'][0]) ? $FileInfo['tags']['vorbiscomment']['title'][0] : '';
            $artist = isset($FileInfo['tags']['vorbiscomment']['artist'][0]) ? $FileInfo['tags']['vorbiscomment']['artist'][0] : '';
            $album = isset($FileInfo['tags']['vorbiscomment']['album'][0]) ? $FileInfo['tags']['vorbiscomment']['album'][0] : '';
            $albumartist = isset($FileInfo['tags']['vorbiscomment']['albumartist'][0]) ? $FileInfo['tags']['vorbiscomment']['albumartist'][0] : '';
        } elseif ($type == 'wav' || $type == 'mp3') {
            $music_name = isset($FileInfo['tags']['id3v2']['title'][0]) ? $FileInfo['tags']['id3v2']['title'][0] : '';
            $artist = isset($FileInfo['tags']['id3v2']['artist'][0]) ? $FileInfo['tags']['id3v2']['artist'][0] : '';
            $album = isset($FileInfo['tags']['id3v2']['album'][0]) ? $FileInfo['tags']['id3v2']['album'][0] : '';
            $albumartist = isset($FileInfo['tags']['id3v2']['albumartist'][0]) ? $FileInfo['tags']['id3v2']['albumartist'][0] : '';
        }
    }
    if (!isset($music_name) || $music_name == null) {
        $music_name = preg_split("/.(?=[^.]*$)/", $file_name)[0];
        $music_name = explode('.', $music_name)[1];
        $Special_characters = [
            '【' => '',
            '】' => '',
            '〖' => '',
            '〗' => '',
            '「' => '',
            '」' => '',
            '『' => '',
            '』' => '',
            '（' => '',
            '）' => '',
            '(' => '',
            ')' => '',
            '/' => '',
            '-' => ''
        ];
        $pattern = '【|】|〖|〗|「|」|『|』|（|）|(|)|\/';
        if (preg_match( '/'.$pattern.'/i', $music_name)) $music_name = strtr($music_name,$Special_characters);
        $music_name = trim($music_name);
    }
    $album = isset($album) ? $album : '';
    $artist = isset($artist) ? $artist : '';
    $data_1 = musicKeywordsApi($api, urlencode(trim($album . ' ' . $albumartist)));
    $data_2 = musicKeywordsApi($api, urlencode($music_name));
    $data_3 = musicKeywordsApi($api, urlencode($artist));
    $data_4 = musicKeywordsApi($api, urlencode(trim($music_name . ' ' . $album)));
    $s2t = $_POST['opencc'] ? opencc_convert($music_name, $oc_s2t) : null;
    $t2s = $_POST['opencc'] ? opencc_convert($music_name, $oc_t2s) : null;
    if (matchMusic($data_1, $artist, $music_name, $duration, $api, $file_path, $file_name, $name_precision, $artist_precision, $duration_precision, $_POST['info'])) {
    } elseif (matchMusic($data_2, $artist, $music_name, $duration, $api, $file_path, $file_name, $name_precision, $artist_precision, $duration_precision, $_POST['info'])) {
    } elseif (matchMusic($data_3, $artist, $music_name, $duration, $api, $file_path, $file_name, $name_precision, $artist_precision, $duration_precision, $_POST['info'])) {
    } elseif (matchMusic($data_4, $artist, $music_name, $duration, $api, $file_path, $file_name, $name_precision, $artist_precision, $duration_precision, $_POST['info'])) {
    } elseif (matchMusic($data_2, $artist, $s2t, $duration, $api, $file_path, $file_name, $name_precision, $artist_precision, $duration_precision, $_POST['info'])) {
    } elseif (matchMusic($data_3, $artist, $s2t, $duration, $api, $file_path, $file_name, $name_precision, $artist_precision, $duration_precision, $_POST['info'])) {
    } elseif (matchMusic($data_4, $artist, $s2t, $duration, $api, $file_path, $file_name, $name_precision, $artist_precision, $duration_precision, $_POST['info'])) {
    } elseif (matchMusic($data_2, $artist, $t2s, $duration, $api, $file_path, $file_name, $name_precision, $artist_precision, $duration_precision, $_POST['info'])) {
    } elseif (matchMusic($data_3, $artist, $t2s, $duration, $api, $file_path, $file_name, $name_precision, $artist_precision, $duration_precision, $_POST['info'])) {
    } elseif (matchMusic($data_4, $artist, $t2s, $duration, $api, $file_path, $file_name, $name_precision, $artist_precision, $duration_precision, $_POST['info'])) {
    } else { echo '<p style="color:#fff;background:#ca0000;margin: 5px;padding: 5px;">失败（无匹配项）：' . preg_split("/.(?=[^.]*$)/", $file_name)[0] . '.lrc</p>'; }
}

/**
 * @param $data 从 API 获取到的一堆歌曲信息
 * @param $artist getis3 获取到的 艺术家 信息
 * @param $music_name getis3 获取到的 歌曲 名字
 * @param $path 歌词生成路径
 * @param $file_name 文件名（完整，带后缀）
 * @return string 是否成功匹配到了歌曲
 */
function matchMusic($data, $artist, $music_name, $duration, $url, $path, $file_name, $name_precision, $artist_precision, $duration_precision, $info) {
    if ($data == null || $music_name == null) return false;
    $song = $data->songs;
    for ($i = 0;$i < count($song);$i++) {
        $api_music_id = $song[$i]->artists[0]->id;
        $api_music_artist = $song[$i]->artists[0]->name;
        $api_music_name = $song[$i]->name;
        $api_music_id = $song[$i]->id;
        $api_music_duration = $song[$i]->duration;
        // 艺术家相似度
        similar_text(strtolower($api_music_artist), strtolower($artist), $similar_artist);
        // 歌曲名相似度
        similar_text(strtolower($api_music_name), strtolower($music_name), $similar_music_name);
        // 歌曲时长相似度
        $api_duration = floor($api_music_duration/1000);
        // 详细的匹配信息
        if ($info) {
            echo '<div style="margin: 5px;padding: 5px;border: 1px solid #999;">';
            echo '<p style="font-size: 0.9em;line-height: 1.1em;"><span style="display: inline-block;width: 7em;">API歌曲ID：</span>'.$api_music_id.'</p>';
            echo '<p style="font-size: 0.9em;line-height: 1.1em;"><span style="display: inline-block;width: 7em;">本地歌曲名：</span>'.$music_name.'</p>';
            echo '<p style="font-size: 0.9em;line-height: 1.1em;"><span style="display: inline-block;width: 7em;">API歌曲名：</span>'.$api_music_name.'</p>';
            echo '<p style="font-size: 0.9em;line-height: 1.1em;"><span style="display: inline-block;width: 7em;">本地艺术家：</span>'.$artist.'</p>';
            echo '<p style="font-size: 0.9em;line-height: 1.1em;"><span style="display: inline-block;width: 7em;">API艺术家：</span>'.$api_music_artist.'</p>';
            echo '<p style="font-size: 0.9em;line-height: 1.1em;"><span style="display: inline-block;width: 7em;">本地时长：</span>'.$duration.'</p>';
            echo '<p style="font-size: 0.9em;line-height: 1.1em;"><span style="display: inline-block;width: 7em;">API时长：</span>'.$api_duration.'</p>';
            echo '<p style="font-size: 0.9em;line-height: 1.1em;"><span style="display: inline-block;width: 7em;">歌曲名相似度：</span>'.$similar_music_name.'</p>';
            echo '<p style="font-size: 0.9em;line-height: 1.1em;"><span style="display: inline-block;width: 7em;">艺术家相似度：</span>'.$similar_artist.'</p>';
            echo '</div>';
        }
        // 匹配判断
        if ($similar_music_name >= $name_precision && $similar_artist >= $artist_precision && $duration >= $api_duration - $duration_precision && $duration <= $api_duration + $duration_precision) {
            musicLrcApi($url, $api_music_id, $path, $file_name);
            return true;
        }
    }
    return false;
}

/**
 * @param $str 需要被去除垃圾的歌词
 * @return string 干净的字符串
 */
function clean($str) {
    $temp_str_back = trim($str);
    $temp_str_start = mb_substr($temp_str_back, 0, 1);
    $temp_str_end = mb_substr($temp_str_back, -1);
    $temp_str_2 = $temp_str_start.$temp_str_end;
    $pattern = '【|】|〖|〗|「|」|『|』|（|）|(|)|\/';
    if (preg_match( '/'.$pattern.'/i', $temp_str_2)) {
        $str = cleanAdd_1($str, '【', '】');
        $str = cleanAdd_1($str, '〖', '〗');
        $str = cleanAdd_1($str, '「', '」');
        $str = cleanAdd_1($str, '『', '』');
        $str = cleanAdd_1($str, '（', '）');
        $str = cleanAdd_1($str, '\(', '\)');
        $str = cleanAdd_2($str, '/');
    }
    return $str;
}

/**
 * clean 的延伸
 * @param $str 去除时间轴的字符串
 * @param $t_str_1 目标字符一
 * @param $t_str_2 目标字符二
 * @return string 干净的字符串
 */
function cleanAdd_1($str, $t_str_1, $t_str_2) {
    $start = mb_substr($str, 0, 1);
    $end = mb_substr($str, -1);
    if ($start == $t_str_1 && $end == $t_str_2) {
        $str = mb_substr($str, 1);
        $str = mb_substr($str, 0, -1);
    } else if ($start == $t_str_1 && $end != $t_str_2 && !preg_match('/'.$t_str_2.'/i', $str)) {
        $str = mb_substr($str, 1);
    } else if ($start != $t_str_1 && $end == $t_str_2 && !preg_match('/'.$t_str_1.'/i', $str)) {
        $str = mb_substr($str, 0, -1);
    }
    return $str;
}

/**
 * clean 的延伸
 * @param $str 去除时间轴的字符串
 * @param $t_str 目标字符
 * @return string 干净的字符串
 */
function cleanAdd_2($str, $t_str) {
    $start = mb_substr($str, 0, 1);
    $end = mb_substr($str, -1);
    if ($start == $t_str) $str = mb_substr($str, 1);
    if ($end == $t_str) $str = mb_substr($str, 0, -1);
    return $str;
}

/**
 * @param $method 歌曲关键字
 * @return string 从 API 获取到的结果
 */
function musicKeywordsApi($url, $keywords) {
    $url = $url.'/search?keywords='.$keywords;
    if (!$data = json_decode(file_get_contents($url))) die('<p style="color:#fff;background:#ca0000;margin: 5px;padding: 5px;text-align: center;">API Error</p>');
    if ($data->code == 200 && $data->result->songCount != 0) {
        return $data->result;
    } else {
        return null;
    }
}

/**
 * @param $id 歌曲 ID
 * @param $path 歌词生成路径
 * @param $file_name 文件名（完整，带后缀）
 */
function musicLrcApi($url, $id, $path, $file_name) {
    $url = $url.'/lyric?id='.$id;
    $data = json_decode(file_get_contents($url));
    $obj = null;
    if ($data->code != 200) {
        echo '<p style="color:#fff;background:#ca0000;margin: 5px;padding: 5px;">失败（接口 err）：' . preg_split("/.(?=[^.]*$)/", $file_name)[0] . '.lrc</p>';
        return;
    }
    if (isset($data->lrc)) {
        if (isset($data->tlyric) && isset($data->tlyric->lyric)) {
            $obj['lrc'] = $data->lrc->lyric;
            $obj['translrc'] = $data->tlyric->lyric;
        } else {
            $obj['lrc'] = $data->lrc->lyric;
        }
        generateLrc($obj, $path, $file_name);
    } else {
        echo '<p style="color:#fff;background:#ca0000;margin: 5px;padding: 5px;">失败（无歌词）：' . preg_split("/.(?=[^.]*$)/", $file_name)[0] . '.lrc</p>';
    }
}

/**
 * @param $obj 从 API 获取到的歌词
 * @param $path 歌词生成路径
 * @param $file_name 文件名（完整，带后缀）
 */
function generateLrc($obj, $path, $file_name) {
    // 清除原歌词中多余废话
    $lrc = explode("\n", $obj['lrc']);
    $lrc_flag = 1;
    $count = count($lrc);
    for ($i = 0; $i < $count; $i++) {
        if (mb_substr($lrc[$i], 0, 1) != '[') {
            $lrc[$i] = trim($lrc[$i]);
            continue;
        }
        $temp_str = mb_substr($lrc[$i], 1, 2);
        $lrc_clear = isset(preg_split("/\](?=[^\]]*$)/", $lrc[$i])[1]) ? trim(preg_split("/\](?=[^\]]*$)/", $lrc[$i])[1]) : '';
        if (!is_numeric($temp_str) || preg_match("/:/i", $lrc_clear) || preg_match("/：/i", $lrc_clear)) {
            unset($lrc[$i]);
            continue;
        } elseif ($lrc_clear == null && $lrc_flag) {
            unset($lrc[$i]);
            continue;
        } elseif ($lrc_flag) {
            $lrc_flag = 0;
        }
        // 修正时间轴并清空歌词首尾空格
        preg_match("/^[^\]]*\]/", $lrc[$i], $lrc_str);
        $lrc_str = strlen($lrc_str[0]) > 10 ? substr_replace($lrc_str[0], "", 9, 1) : $lrc_str[0];
        $lrc[$i] = $lrc_str.$lrc_clear;
    }
    $lrc = array_values($lrc);  // 索引归零

    // 清除翻译中多余废话
    if (isset($obj['translrc']) && $_POST['trans']) {
        $translrc = explode("\n", $obj['translrc']);
        $count = count($translrc);
        for ($i = 0; $i < $count; $i++) {
            if (mb_substr($translrc[$i], 0, 1) != '[') {
                $translrc[$i] = trim($translrc[$i]);
                continue;
            }
            $temp_str = mb_substr($translrc[$i], 1, 2);
            $translrc_clear = isset(preg_split("/\](?=[^\]]*$)/", $translrc[$i])[1]) ? trim(preg_split("/\](?=[^\]]*$)/", $translrc[$i])[1]) : '';
            if (!is_numeric($temp_str) || preg_match("/:/i", $translrc_clear) || preg_match("/：/i", $translrc_clear)) {
                unset($translrc[$i]);
                continue;
            } elseif ($translrc_clear == null) {
                unset($translrc[$i]);
                continue;
            }
            // 修正时间轴并清空歌词首尾空格
            preg_match("/^[^\]]*\]/", $translrc[$i], $translrc_str);
            $translrc_str = strlen($translrc_str[0]) > 10 ? substr_replace($translrc_str[0], "", 9, 1) : $translrc_str[0];
            $translrc[$i] = $translrc_str.$translrc_clear;
        }
        $translrc = array_values($translrc);  // 索引归零
    }

    // 最终歌词数组
    $final_lrc = [];

    // 原歌词与翻译拼接：样式一
    if ($_POST['style'] === '1') {
        if (isset($obj['translrc']) && $_POST['trans']) {
            for ($i = 0; $i < count($lrc); $i++) {
                $flag = 0;
                $lrc_temp = mb_substr($lrc[$i], 1, 8);
                for ($j = 0; $j < count($translrc); $j++) {
                    $translrc_temp = mb_substr($translrc[$j], 1, 8);
                    if ($lrc_temp == $translrc_temp  && strlen($translrc[$j]) > 10) {
                        $translrc_temp_2 = preg_split("/\](?=[^\]]*$)/", $translrc[$j])[1];
                        $translrc_temp_2 = clean($translrc_temp_2);
                        $flag = 1;
                        break;
                    }
                }
                if ($flag) {
                    if ($lrc[$i] != null) {
                        array_push($final_lrc,$lrc[$i]." 「".$translrc_temp_2."」\n");
                    }
                } else {
                    if ($lrc[$i] != null) {
                        array_push($final_lrc,$lrc[$i]."\n");
                    }
                }
            }
        } else if (isset($obj['lrc'])) {
            for ($i = 0; $i < count($lrc); $i++) {
                if ($lrc[$i] != null) {
                    array_push($final_lrc,$lrc[$i]."\n");
                }
            }
        }
    }

    // 原歌词与翻译拼接：样式二
    if ($_POST['style'] === '2') {
        if (isset($obj['translrc']) && $_POST['trans']) {
            for ($i = 0;$i < count($lrc);$i++) {
                if ($lrc[$i] != null) {
                    $lrc_temp = mb_substr($lrc[$i],1,8);
                    array_push($final_lrc, $lrc[$i])."\n";
                }
                for ($j = 0;$j < count($translrc);$j++) {
                    if ($translrc[$j] != null) {
                        $translrc_temp = mb_substr($translrc[$j],1,8);
                        if ($lrc_temp == $translrc_temp && $translrc[$j] != null) {
                            $translrc_temp_2 = preg_split("/\](?=[^\]]*$)/", $translrc[$j]);
                            $translrc_temp_2_back = clean($translrc_temp_2[1]);
                            array_push($final_lrc, ($translrc_temp_2[0].']'.$translrc_temp_2_back) . "\n");
                            break;
                        }
                    }
                }
            }
        } else if (isset($data['lrc'])) {
            for ($i = 0;$i < count($lrc);$i++) {
                if ($lrc[$i] != null) {
                    array_push($final_lrc, $lrc[$i]."\n");
                }
            }
        }
    }

    // 创建歌词
    $lrc_name = preg_split("/.(?=[^.]*$)/", $file_name)[0].'.lrc';
    if (!$file = fopen($path . '/' . $lrc_name, "w")) echo '<p style="color:#fff;background:#ca0000;margin: 5px;padding: 5px;">失败：'.$lrc_name.'</p>';
    if (fwrite($file, implode($final_lrc))) echo '<p style="color:#fff;background:#006d00;margin: 5px;padding: 5px;">成功：'.$lrc_name.'</p>';
    fclose($file);
}