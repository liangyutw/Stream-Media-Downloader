var BodyParser = require( 'body-parser' );
var func = require( './func.js' );
var router = require( 'router' )();

function route()
{
    router.use( BodyParser.text() );
    router.post( '/todo', func.createItem );
    router.get( '/todo', func.readList );
    router.get( '/todo/:id', func.readItem );
    router.delete( '/todo/:id', func.deleteItem );
    router.put( '/todo/:id', func.updateItem );
}

exports.route = route;
exports.routerObj = router;