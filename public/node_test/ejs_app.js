// express web framework
var express = require('express');
//讀取 posts.json
var fs = require('fs');
var app = express();


//放置靜態網頁
// app.use('/node_test', express.static(__dirname + '/node_test'));


//定義網站標題
app.locals.title="Get json data using express web framework";


app.all('*', function(req, res, next){
  fs.readFile('posts.json', function(err, data){
    res.locals.posts = JSON.parse(data);
    next();
  });
});

//網頁主進入點
app.get('/', function(req, res){
  //指定 /views/idex.ejs
  res.render('index.ejs');
});

//顯示 posts.json 資料
app.get('/api/posts', function(req, res){
  res.json(res.locals.posts);
});

//當 url 是 /post/:id 時, 取得某一筆資料
app.get('/post/:id', function(req, res, next){
  //取得 post.json 資料夾
  res.locals.posts.forEach(function(post){
    //從 url 取得 id 參數與 posts.json 裡的 id
    if (req.params.id === post.id){
      //顯示參數為  url 中 id 的 post.id, 那麼顯示部分資料
      res.render('post.ejs', { post: post });
    }
  })
});


app.listen(3000);
console.log('app is listening at localhost:3000...');