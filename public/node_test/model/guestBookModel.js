var mongodb = require('mongodb').Db,
    Server = require('mongodb').Server;

var mongodbServer = new Server('localhost', 27017, { auto_reconnect: true, poolSize: 10 });
var db_name = 'demo';
var table_name = 'forum_subject';
var db = new mongodb(db_name, mongodbServer);



function insert_message(page, insert_data, callback)
{
    var option = {};

    db.open(function(err,db){
        if(!err){


            if (insert_data.table) {
                table_name = insert_data.table;
            }
            db.collection(table_name, function(err, collection) {
                collection.insert(insert_data, option, function(err, result) {
                    callback(result);
                    db.close();
                });
            });
        }
    });
}

function list_message(page, callback)
{
    var option = {
        skip:(page-1)*5,
        limit:5
    };
    var param = {};
    param.page = page;
    db.open(function(err,db){
        if (err) {
            mongodb.close();
            callback(err);
        } else {

            db.collection('forum_subject', function(err, collection) {
                collection.count(function(err, count) {
                    param.total = count;
                })
                collection.find({}, option).sort({insert_date: -1}).toArray(function(err, docs) {
                    param.data = docs;

                    callback(param);
                    db.close();
                });
            });
        }
    });
}

function get_one_message(uuid, callback)
{
    var option = param = {};
    db.open(function(err,db){
        if (err) {
            mongodb.close();
            callback(err);
        } else {

            db.collection('forum_subject', function(err, collection) {
                collection.find({"uuid":uuid}, option).toArray(function(err, docs) {

                    param.data = docs;

                    callback(param);
                    db.close();
                });

            });
        }
    });
}

function get_subject_data(uuid, callback)
{
    var option = {};
    var reply_data = {};

    db.open(function(err,db){
        if (err) {
            mongodb.close();
            callback(err);
        } else {


            db.collection(table_name, function(err, collection) {
                collection.find({"uuid":uuid}, {}).toArray( function(err, doc) {

                    //發表主題資料
                    reply_data.subject_creator_data = doc;
                    db.close();

                });
            });


            db.collection('forum_reply', function(err, collection) {
                collection.find({"subject_uuid":uuid}, {}).toArray(function(err, reply) {

                    //回應主題資料
                    reply_data.reply_data = reply;

                    callback(reply_data);
                    db.close();
                });
            });


        }
    });
}

function update_message(uuid, update_fields, callback)
{
    var where_condition = {
        uuid:uuid
    };

    db.open(function(err,db){

        if (err) {
            mongodb.close();
            callback(err);
        } else {

            db.collection(db_name, function(err, collection) {
                collection.update(where_condition, update_fields, function(err, result) {
                    callback(result);
                    db.close();
                });
            });
        }
    });
}

function delete_message(uuid, callback)
{
    var where_condition = {
        uuid:uuid
    };

    db.open(function(err,db){

        if (err) {
            mongodb.close();
            callback(err);
        } else {

            db.collection(db_name, function(err, collection) {
                collection.remove(where_condition, {}, function(err, result) {
                    callback(result);
                    db.close();
                });
            });
        }
    });
}

exports.insert_message = insert_message;
exports.list_message = list_message;
exports.get_one_message = get_one_message;
exports.update_message = update_message;
exports.delete_message = delete_message;
exports.get_subject_data = get_subject_data;