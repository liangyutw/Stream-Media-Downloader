var fs = require('fs');
var formidable = require("formidable");
var exec = require("child_process").exec;
var encode = "utf8";
var msg = '';
var data = {};
var url = require("url");
var productModel = require("./model/productModel.js");


function start(response) {

    exec("find /",
    { timeout: 10000, maxBuffer: 20000*1024 },
    function (error, stdout, stderr) {
        // console.log(error);return false;
      response.writeHead(200, {"Content-Type": "text/plain"});
      response.write(stdout);
      response.end();
    });

}

function file_list(request, response) {
    // var testFolder = './files/';
    // var list = '';

    // fs.readdir(testFolder, function(err, files) {
    //     if (files.length == 0) {
    //         list = '暫無資料';
    //     } else{
    //         files.forEach(file => {
    //             // console.log(file);
    //             list += file+' <a href="/rename/'+file+'">變更檔名</a>  <a href="/delete/'+file+'">刪除檔案</a><BR>';
    //         });
    //     }

    //     response.writeHead(200, {
    //         'Content-Type': 'text/html'
    //     });
    //     response.write(list);
    //     response.end();
    // });
    // var list = productModel.list_data();
    // console.log(list);return false;

    // response.writeHead(200, {
    //     'Content-Type': 'text/html'
    // });
    // response.write(list);
    // response.end();


}

function delete_file(request, response, callback) {

    //檢查檔案是否存在
    fs.exists('files/'+request.params.name, function(exists) {
        // console.log(fields);return false;
        if(!exists) {
            msg = '無此檔案! <a href="/list">列表</a>';
            callback(msg);
        } else {
            fs.unlink('files/'+request.params.name, function(err) {

                // output(response, '刪除檔案完成!');
                msg = '刪除檔案完成!';
                callback(msg);
            });
        }
    });



    //回應到 upload.html，有重整效果
    //msg經由callback讓 server.js使用會暫存，重整後才顯示表單執行後的結果
    //加上這段會在 server.js暫存後才執行
    response.status(200).sendFile(__dirname +'/page/delete.html');
}

function post_rename(request, response, callback)
{
    //建立傳入表單物件
    var form = new formidable.IncomingForm();
    //解析表單資料，解成 3個參數(錯誤、欄位、檔案)
    form.parse(request, function (err, fields, files) {
        // console.log(fields);return false;

        //檢查檔案是否存在
        fs.exists('files/'+fields.old_file_name, function(exists) {
            // console.log(fields);return false;
            if(!exists) {
                msg = '無此檔案! <a href="/list">列表</a>';
                callback(msg);
            } else {
                if (typeof fields == 'object' && fields.new_file_name != '') {

                    fs.rename('files/'+fields.old_file_name, 'files/'+fields.new_file_name+'.epub', function (err) {
                        if (err) throw err;
                        msg = '修改檔名完成! <a href="/list">列表</a>';
                        callback(msg);
                        // output(response, '修改檔名完成!');
                    });
                }
            }
        });


    });

    //回應到 upload.html，有重整效果
    //msg經由callback讓 server.js使用會暫存，重整後才顯示表單執行後的結果
    //加上這段會在 server.js暫存後才執行
    response.status(200).sendFile(__dirname +'/page/rename.html');
}
function get_rename(request, response)
{
    fs.readFile('page/rename.html', encode, function(err, file) {

        response.writeHead(200, {
            'Content-Type': 'text/html'
        });
        response.write(file);
        response.end();

    });
}

function get_upload(request, response)
{
    var pathname = url.parse(request.url, true).pathname;

    fs.readFile('page'+pathname+'.html', encode, function(err, file) {
        // console.log(file);return false;
        response.writeHead(200, {
            'Content-Type': 'text/html'
        });
        response.write(file);
        response.end();

    });
}

function post_upload(request, response, callback)
{
    // var parse = url.parse(request.url, true);
    //建立傳入表單物件
    var form = new formidable.IncomingForm();

    //解析表單資料，解成 3個參數(錯誤、欄位、檔案)
    form.parse(request, function (err, fields, files) {

        //上傳檔案
        if ((Object.keys(files).length <= 0 || files.img.name == '')  && typeof files == 'object') {
            msg = '沒有選擇檔案';
            callback(msg);
        }else{

            //檢查檔案是否存在
            fs.exists('files/'+files.img.name, function(exists) {

                if (exists) {
                    msg = '檔案已存在';
                    callback(msg);
                }else{
                    //檔案不存在，進行複製
                    //使用單一管線複製檔案
                    fs.createReadStream(files.img.path).pipe(fs.createWriteStream('files/'+files.img.name));

                    //再檢查一次檔案是否上傳完成
                    fs.exists('files/'+files.img.name, function(exists) {
                        if (exists) {
                            data.name = files.img.name;
                            data.size = files.img.size;
                            data.type = files.img.type;


                            productModel.insert_data(data, request, response);
                            msg = '檔案已上傳';
                            callback(msg);
                        }
                    });
                }
            });
        }

    });

    //回應到 upload.html，有重整效果
    //msg經由callback讓 server.js使用會暫存，重整後才顯示表單執行後的結果
    //加上這段會在 server.js暫存後才執行
    response.status(200).sendFile(__dirname +'/page/upload.html');
}


function output(response, msg)
{
    response.writeHead(200, {"Content-Type": "text/plain"});
    response.write(msg);
    response.end();
}

exports.start = start;
exports.get_upload = get_upload;
exports.post_upload = post_upload;
exports.file_list = file_list;
exports.get_rename = get_rename;
exports.post_rename = post_rename;
exports.delete_file = delete_file;
// exports.output_msg = output_msg;
