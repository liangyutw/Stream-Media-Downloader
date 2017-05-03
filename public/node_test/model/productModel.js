var mongodb = require('mongodb');

var mongodbServer = new mongodb.Server('localhost', 27017, { auto_reconnect: true, poolSize: 10 });
var db_name = 'demo';
var db = new mongodb.Db(db_name, mongodbServer);
var list = '';


function insert_data(data, request, response)
{
    db.open(function(err,db){
        if(err){
            response.writeHead(200, {
                'Content-Type': 'text/html'
            });
            response.write('系統出錯');
            response.end();
        } else {

            db.collection(db_name, function(err, collection) {
                // collection.remove();

                collection.insert(data, {}, function(err, result) {
                    db.close();
                });
            });

        }
    });
}

function list_data(request, response)
{
    db.open(function(err,db){
        if(!err){
            db.collection(db_name, function(err, collection) {
                collection.find().toArray(function(err, docs) {
                    // console.log(docs);
                    if (docs.length <= 0) {
                        response.writeHead(200, {
                            'Content-Type': 'text/html'
                        });
                        response.write('查無資料');
                        response.end();
                    }
                    for (var index in docs) {
                        var file = docs[index];
                        // console.log(file);
                        list += file.name+'('+file.size+' KB) <a href="/rename/'+file.name+'">變更檔名</a>  <a href="/delete/'+file.name+'">刪除檔案</a><BR>';
                    }
                    response.writeHead(200, {
                        'Content-Type': 'text/html'
                    });
                    response.write(list);
                    response.end();
                    db.close();
                });
            });
        }else{
            response.writeHead(200, {
                'Content-Type': 'text/html'
            });
            response.write('系統出錯');
            response.end();
        }
    });

}

exports.insert_data = insert_data;
exports.list_data = list_data;
// exports.list = list;