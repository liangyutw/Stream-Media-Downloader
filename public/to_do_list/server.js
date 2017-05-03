
var Http = require( 'http' ),
    server;


/**
 * 伺服器執行
 * @param  {[type]} request  [description]
 * @param  {[type]} response )             {    router( request, response, function( error ) {    if ( !error ) {        response.writeHead( 404 );    } else {                console.log( error.message, error.stack );        response.writeHead( 400 );    }    response.end( 'RESTful API Server is running!' );    });} [description]
 * @return {[type]}          [description]
 */
function start(router)
{

    function onRequest( request, response ) {
        router( request, response, function( error ) {
        if ( !error ) {
            response.writeHead( 404 );
        } else {
            // Handle errors
            console.log( error.message, error.stack );
            response.writeHead( 400 );
        }
        response.end( 'RESTful API Server is running!' );
        });
    }
    server = Http.createServer(onRequest).listen(3000, function () {
        console.log( 'Listening on port 3000' );
    });
}

exports.start = start;
