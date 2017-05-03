var mongodb = require('mongodb');

var mongodbServer = new mongodb.Server('localhost', 27017, { auto_reconnect: true, poolSize: 10 });
var db_name = 'demo';
var db = new mongodb.Db(db_name, mongodbServer);
var stu = '';


function list_data(request, response)
{
db.open(function(err,db){
    if(!err){
        console.log("We are connected");

// Create a collection we want to drop later
  db.collection(db_name, function(err, collection) {
    // collection.remove();

    // Insert a bunch of documents for the testing
    // collection.insert([
    //     {
    //         "name":"qwe",
    //         "age":"12",
    //         "address":{
    //             "zipcode":"105",
    //             "street":"光復北路11巷44號13樓"
    //         },
    //         "insert_date":"2017-04-18"
    //     },
    //     {
    //         "name":"wsx",
    //         "age":"10",
    //         "address":{
    //             "zipcode":"105",
    //             "street":"光復北路11巷44號10樓"
    //         },
    //         "insert_date":"2017-04-17"
    //     },
    //     {
    //         "name":"zxc",
    //         "age":"14",
    //         "address":{
    //             "zipcode":"105",
    //             "street":"光復北路11巷44號3樓"
    //         },
    //         "insert_date":"2017-04-10"
    //     }
    // ], {}, function(err, result) {


      collection.find().toArray(function(err, docs) {
        // console.log(docs);

        for (var index in docs) {
            stu += docs[index].name;
            // console.log(stu.name);
        }

    response.writeHead(200, {
        'Content-Type': 'text/html'
    });
    response.write(stu);
    response.end();
        // return docs;
        db.close();
      });

    // });
  });

        // db.collection(db_name, function(err, collect) {
        //     collect.find().toArray(function(err, data) {
        //         console.log(data);
                // for (var index in data) {
                //     var stu = data[index];
                //     console.log(stu);
                // }
                /* Found this People */
                // if (data) {
                //     console.log(data);
                // } else {
                //     console.log('Cannot found');
                // }
        //     });
        // });
    }
});

}

exports.list_data = list_data;