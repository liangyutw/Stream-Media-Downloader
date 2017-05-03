// var http = require('http');
// var url = require("url");
// var router = require("./router.js");
var requestHandlers = require("./requestHandlers.js");

// var fs = require('fs');
// var encode = "utf8";
// var formidable = require("formidable");

var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);

var msg = '';
var port = 4000;

// function start() {


    // function onRequest(request, response) {

    //     var parse = url.parse(request.url, true);
    //     var content = router.route(parse, request, response);
    // }

    //檔案上傳
    app.route('/upload')
        .get(function(request, response){
            requestHandlers.get_upload(request, response);
        })
        .post(function(request, response){
            requestHandlers.post_upload(request, response, function(return_msg){

                //回傳值塞給 msg，由下面的 emit 丟回畫面
                msg = return_msg;
            });
        });

    //檔案列表
    app.route('/list').get(function(request, response){
        // requestHandlers.file_list(request, response);
        var peoductModel = require("./model/productModel.js");
        peoductModel.list_data(request, response);
    });

    //刪除檔案
    app.route('/delete/:name').get(function(request, response){
        // console.log(request.params);return false;
        requestHandlers.delete_file(request, response, function(return_msg){
            //回傳值塞給 msg，由下面的 emit 丟回畫面
            msg = return_msg;
        });
    });

    //修改檔名
    app.route('/rename/:name')
        .get(function(request, response){
            requestHandlers.get_rename(request, response);
        })
        .post(function(request, response){
            // console.log(request);return false;
            requestHandlers.post_rename(request, response, function(return_msg){

                //回傳值塞給 msg，由下面的 emit 丟回畫面
                msg = return_msg;
            });
        });

    io.on('connection', function(socket){
        // socket.on('chat message', function(msg){

            io.emit('chat message', msg);
        // });

        socket.on('disconnect', function(){
            console.log('user disconnected');
            socket.emit('server_msg', 'disconnected');
        });


    });

    // io.on('disconnect', function(socket){
    //     io.emit('disconnect', 'disconnected');
    // });

    http.listen(port);
    console.log("Server has started. Listen on "+port);
// }



// exports.start = start;