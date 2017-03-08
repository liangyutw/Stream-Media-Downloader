線上串流媒體下載、切割、快照、mp3<BR>
主要透過幾個工具所執行<BR>
<BR>
Base OS：Cent OS<BR>
後端程式：PHP<BR>
前端應用js：Jquery<BR>
框架：Laravel 5.1<BR>
<BR>
必需安裝在 OS 裡的工具<BR>
1.youtube-dl<BR>
2.Node.js<BR>
3.Socket.io<BR>
4.Express<BR>
5.FFmpeg<BR>
<br>
主要程式：<br>
app/Http/Contorller/YoutubeController.php<BR>
app/Http/routes.php<BR>
public/YouTubeDownloadListener.js<BR>
resources/views/youtube/home.blade.php<BR>
<BR>
主要開發者：https://github.com/telunyang/Video_Downloader_Splitter_Converter<br>
<br>
說明：此版本是個人的改造版(網頁顯示修改)

--update 2017-01-10--<BR>
更新頁面操作功能、增加合併影片、重製影片、修改檔名等功能<BR>
<BR>
--update 2017-01-13--<BR>
新增按分鐘數切割影片功能<BR>

--update 2017-01-27--<BR>
新增聊天室功能

--update 2017-02-10--<BR>
修改聊天室寫重覆訊息，新增聊天室傳圖片、檔案、下載<br>新增推播功能、寄信功能(nodemail)

--update 2017-03-08--<BR>
修改聊天室功能(歷史訊息、限制建立聊天室數量、改寫聊天室訊息寫入json的格式、增加錯誤訊息顯示)
