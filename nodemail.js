
var app = require('express');
var http = require('http').Server(app);
var io = require('socket.io')(http);
var Redis = require('ioredis');
var redis = new Redis();

redis.subscribe('register_mail', function(err, count) {
    console.log('connect!');
});

io.on('connection', function(socket) {

    socket.on('set-token', function(token) {
        console.log(token);
        socket.join('token:' + token);
    });
});

redis.on('message', function(channel, notification) {
    // 將訊息推播給使用者
    io.emit('register_mail', {"return":1});

    notification = JSON.parse(notification);
    //console.log(notification.data);

    var nodemailer = require('nodemailer');
    var smtpTransport = require('nodemailer-smtp-transport');

    var transporter = nodemailer.createTransport(
        smtpTransport({
            host: 'smtp.office365.com',
            port: 587,
            auth: {
                user: 'noreply@bookwalker.com.tw',
                pass: 'Welcome2'
            }
        })
    );

    // 設定收件者，信件內容
    var options = {
        //寄件者
        from: 'BOOK☆WALKER 系統信箱 <noreply@bookwalker.com.tw>',
        //收件者
        to: notification.data.register_name +' <'+notification.data.register_email+'>',
        //副本
        cc: '',
        //密件副本
        bcc: '',
        //主旨
        subject: notification.data.register_subject,
        //純文字
        text: '',
        //嵌入 html 的內文
        html: '[由Laravel推播服務寄送]'+notification.data.register_message,
        //附件檔案
        attachments: [
            // {
            //     filename: 'text01.txt',
            //     content: '聯候家上去工的調她者壓工，我笑它外有現，血有到同，民由快的重觀在保導然安作但。護見中城備長結現給都看面家銷先然非會生東一無中；內他的下來最書的從人聲觀說的用去生我，生節他活古視心放十壓心急我我們朋吃，毒素一要溫市歷很爾的房用聽調就層樹院少了紀苦客查標地主務所轉，職計急印形。團著先參那害沒造下至算活現興質美是為使！色社影；得良灣......克卻人過朋天點招？不族落過空出著樣家男，去細大如心發有出離問歡馬找事'
            // }
            // ,
            // {
            //     filename: 'unnamed.jpg',
            //     path: '/Users/Weiju/Pictures/unnamed.jpg'
            // }
        ]
    };

    //發送信件方法
    transporter.sendMail(options, function(error, info){
        if(error){
            console.log(error);
        }else{
            console.log('訊息發送: ' + info.response);
        }
    });

});

http.listen(3001, function() {
    console.log('Listening on Port 3001');
});
// return false;
//
// var nodemailer = require('nodemailer');
// var smtpTransport = require('nodemailer-smtp-transport');
//
// var transporter = nodemailer.createTransport(
//     smtpTransport({
//         host: 'smtp.office365.com',
//         port: 587,
//         auth: {
//             user: 'noreply@bookwalker.com.tw',
//             pass: 'Welcome2'
//         }
//     })
// );
//
// var mail_list = [
//     "chen.liangyu@bookwalker.com.tw",
//     "chu.musih@bookwalker.com.tw"
// ];
//
// for (var i=0; i <= mail_list.length; i++) {
//     //console.log(mail_list[i]);
//     if (mail_list[i] !== undefined) {
//
//
//
//         // 設定收件者，信件內容
//         var options = {
//             //寄件者
//             from: 'BOOK☆WALKER 系統信箱 <noreply@bookwalker.com.tw>',
//             //收件者
//             to: mail_list[i],
//             //副本
//             cc: '',
//             //密件副本
//             bcc: '',
//             //主旨
//             subject: '使用 node.js 發送的測試信件給'+mail_list[i],
//             //純文字
//             text: '',
//             //嵌入 html 的內文
//             html: '<h2>Why and How</h2> <p>聯候家上去工的調她者壓工，我笑它外有現，血有到同，民由快的重觀在保導然安作但。護見中城備長結現給都看面家銷先然非會生東一無中；內他的下來最書的從人聲觀說的用去生我，生節他活古視心放十壓心急我我們朋吃，毒素一要溫市歷很爾的房用聽調就層樹院少了紀苦客查標地主務所轉，職計急印形。團著先參那害沒造下至算活現興質美是為使！色社影；得良灣......克卻人過朋天點招？不族落過空出著樣家男，去細大如心發有出離問歡馬找事</p>',
//             //附件檔案
//             attachments: [
//             {
//                 filename: 'text01.txt',
//                 content: '聯候家上去工的調她者壓工，我笑它外有現，血有到同，民由快的重觀在保導然安作但。護見中城備長結現給都看面家銷先然非會生東一無中；內他的下來最書的從人聲觀說的用去生我，生節他活古視心放十壓心急我我們朋吃，毒素一要溫市歷很爾的房用聽調就層樹院少了紀苦客查標地主務所轉，職計急印形。團著先參那害沒造下至算活現興質美是為使！色社影；得良灣......克卻人過朋天點招？不族落過空出著樣家男，去細大如心發有出離問歡馬找事'
//             }
//             // ,
//             // {
//             //     filename: 'unnamed.jpg',
//             //     path: '/Users/Weiju/Pictures/unnamed.jpg'
//             // }
//             ]
//         };
//
//         //發送信件方法
//         transporter.sendMail(options, function(error, info){
//             if(error){
//                 console.log(error);
//             }else{
//                 console.log('訊息發送: ' + info.response);
//             }
//         });
//     }
// }
