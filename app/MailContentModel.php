<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class MailContentModel extends Model
{
    /**
     * 會員註冊信內容
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public function member_register($url = null)
    {
        if (is_null($url)) {
            return false;
        }

        $subject = "會員註冊驗証信 - BOOK☆WALKER TAIWAN 台灣漫讀";
        $content = '
        <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" style=" font-size: 14px;  line-height: 24px;" >
        <tbody>
          <tr>
            <td width="600px">
            您好，<br>感謝您註冊成為BOOK☆WALKER的會員。<br>為了確認您的電子郵件無誤，請點擊下列網址，完成認證。<br>
            <br>
            <a href=' . $url . ' target="_blank" >' . $url . '</a>
            <br>
            <br>

            <br>
            現在註冊成為會員，即可獲得三項好禮！<br>
            ☆註冊完成即贈50元折價券一張！<br>
            ☆註冊完成立刻上傳暱稱與頭像加贈10點點數！<br>
            ☆每年生日當月均可領取50元折價券一張！<br>
            ※上述連結網址在本信件寄出一小時後將自動失效，請盡早點擊，享受BOOK☆WALKER所提供的閱讀樂趣及多項服務。<br>
            ※如果您點擊上述網址卻無法打開頁面時，請直接複製網址並開啟另一瀏覽器視窗，將連結網址貼至網址列中。<br>
            </td>

          </tr>

          <tr>
            <td height="100"  valign="middle">
              <img src="http://www.bookwalker.com.tw/images/p_img/bw_logo.png">
            </td>
          </tr>

          <tr>
            <td>※本信件是由系統自動產生與發送，請勿直接回覆。<br>※如果您未註冊BOOK☆WALKER的會員卻收到此信件，請無視此信件。
            </td>
          </tr>
        </tbody>
      </table>';

        return array("subject" => $subject, "content" => $content);
    }
}