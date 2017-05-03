var mysql      = require('mysql');
var express = require('express');
var app = express();

var connection = mysql.createConnection({
  host     : 'localhost',
  user     : 'root',
  password : '2ruxarrx',
  database : 'test'
});

app.locals.title="mysql DB to view";

connection.connect();

connection.query('SELECT * from demo', function(err, rows, fields) {
    if (!err){
        // console.log(rows);
app.locals.posts = rows;


        for (var i=0; i<=rows.length;i++) {
            // console.log(rows[i]);
            if (rows[i] != undefined) {
                // var obj = rows[i];
                // console.log(rows[i].id);


// app.all('*', function(req, res, next){

//                   // fs.readFile('posts.json', function(err, data){
//                     res.locals.posts = rows[i];
//                     next();
//                   // });
//                 });



            }
        }
        // console.log('The solution is: ', rows);
    }else{
        console.log('Error while performing Query.');
    }
});



connection.end();


app.get('/mysql_db', function(req, res){
    // console.log(app.locals.posts);
    // res.json(app.locals.posts);
    res.render('index.ejs');
});

app.listen(3000);
console.log('app is listening at localhost:3000...');