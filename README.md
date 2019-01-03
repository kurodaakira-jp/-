# 歌词一键生成脚本

## 本脚本通过调用网上大佬写的 Node.js 版网易云音乐 API ，来实现自动匹配歌曲歌词，生成同名的、纯净的LRC文件

### 说明：该脚本只适用于有详细属性的音乐文件，需要用到的属性有：标题（歌曲名）、参与创作的艺术家、唱片集

### Node.js API 大佬
https://github.com/Binaryify/NeteaseCloudMusicApi

### 运行环境
PHP（我用的是 7+）

### 需要的插件
getID3（用于读取歌曲信息）
PHP 的 OpenCC 拓展（当然，也可以选择不使用 OpenCC）

### 为什么要用 OpenCC
在之前生成歌词的时候（我主要是 Anime 的 Hi-Res 歌曲）一些歌曲是日本汉字（也就是繁体），但是网易上面试简体，所以就会匹配不到歌词，所以。

### 每个歌曲最多匹配四次
第一次：歌曲名+专辑名
第二次：歌曲名
第三次：OpenCC 转简体后的歌曲名+专辑名
第四次：OpenCC 转简体后的歌曲名
（反正总有一次能匹配上 23333）
