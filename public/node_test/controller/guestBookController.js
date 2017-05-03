var guestBookModel = require('../model/guestBookModel.js');

var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);

var msg = '';
var port = 3000;
var formidable = require("formidable");
var path = require('path');
var uuidV4 = require('uuid/v4');
app.locals.comment_reply = app.locals.edit_status = false;

app.set('views', path.join(__dirname, '../views'));


app.route('/')
    .get(function(request, response){
        var page = request.query.p?parseInt(request.query.p):1;

        guestBookModel.list_message(page, function(list_data){
            // console.log(list_data);return false;
            if (list_data.length <= 0) {
                app.locals.posts = false;
            } else {
                app.locals.posts = list_data;
            }
            //指定 /views/idex.ejs
            response.render('guestbook.ejs');
        });
    });

app.route('/leave_message')
    .post(function(request, response){
        var insert_data = {};
        var form = new formidable.IncomingForm();
        var page = request.query.p?parseInt(request.query.p):1;

        form.parse(request, function (err, fields, files) {

            insert_data.uuid = uuidV4();
            insert_data.subject = fields.subject;
            insert_data.nickname = fields.nickname;
            insert_data.message_content = fields.message_content;
            insert_data.insert_date = getTodayDate();

            guestBookModel.insert_message(page, insert_data, function(return_msg){
                response.redirect('/');
            });
        });
    });

app.route('/:uuid')
    .get(function(request, response){
        var uuid = request.params.uuid;

        guestBookModel.get_subject_data(uuid, function(list_data){
// console.log(list_data);
            list_data.subject_creator_data.forEach(function(row){
                app.locals.subject = row.subject;
            });


            app.locals.posts = list_data;
            app.locals.comment_reply = false;

            response.render('subject_reply.ejs');

        });
    });

app.route('/comment/:uuid')
    .get(function(request, response){

        var uuid = request.params.uuid;
        guestBookModel.get_one_message(uuid, function(list_data){

            list_data.data.forEach(function(row){
                app.locals.subject = row.subject;
            });

            app.locals.posts = list_data;
            app.locals.comment_reply = true;
            response.render('subject_reply.ejs');
        });

    });

app.route('/leave_message_for_comment/:uuid')
    .post(function(request, response){
        var insert_data = {};
        var form = new formidable.IncomingForm();
        var subject_uuid = request.params.uuid;
        var page = request.query.p?parseInt(request.query.p):1;

        form.parse(request, function (err, fields, files) {

            insert_data.uuid = uuidV4();
            insert_data.subject_uuid = subject_uuid;
            insert_data.nickname = fields.cmt_nickname;
            insert_data.message_content = fields.cmt_message_content;
            insert_data.insert_date = getTodayDate();
            insert_data.table = 'forum_reply';

            guestBookModel.insert_message(page, insert_data, function(return_msg){
                response.redirect('/');
            });
        });
    });

app.route('/edit/:uuid')
    .get(function(request, response){
        var uuid = request.params.uuid;

        guestBookModel.get_one_message(uuid, function(list_data){
            app.locals.edit_status = true;
            app.locals.posts = list_data;
            response.render('guestbook.ejs');

        });
    }).post(function(request, response){
        var uuid = request.params.uuid;
        var form = new formidable.IncomingForm();

        form.parse(request, function (err, fields, files) {

            var update_fields = {
                $set:{
                    "uuid":uuid,
                    "nickname" : fields.upd_nickname,
                    "message_content" : fields.upd_message_content,
                    "update_date" : getTodayDate()
                }
            };

            guestBookModel.update_message(uuid, update_fields, function(return_msg){
                response.redirect('/');
            });
        });
    });

app.route('/delete/:uuid')
    .get(function(request, response){
        var uuid = request.params.uuid;

        guestBookModel.delete_message(uuid, function(list_data){
            response.redirect('/');
        });
    });



    //取得今天的日期，讓使用者送出訊息時參考用
function getTodayDate() {
    var str = '';

    // 宣告日期物件
    var today = new Date();

    // 年
    var today_year = today.getFullYear();
    str += today_year;

    // 月
    var today_month = today.getMonth() + 1;
    if (today_month >= 10)
        str += '-' + today_month;
    else
        str += '-0' + today_month;

    // 日
    var today_date = today.getDate();
    if (today_date >= 10)
        str += '-' + today_date;
    else
        str += '-0' + today_date;

    var today_hour = today.getHours();
    if (today_hour >= 10)
        str += ' ' + today_hour;
    else
        str += ' 0' + today_hour;

    var today_minute = today.getMinutes();
    if (today_minute >= 10)
        str += ':' + today_minute;
    else
        str += ':0' + today_minute;

    var today_second = today.getSeconds();
    if (today_second >= 10)
        str += ':' + today_second;
    else
        str += ':0' + today_second;
    return str;
}

http.listen(port);
console.log("伺服器運作中，監聽 "+port);