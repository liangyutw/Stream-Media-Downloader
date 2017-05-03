// crud function collection
var counter = 0,
    todoList = {};

/**
 * 建立陣列
 * @param  {[type]} request  [description]
 * @param  {[type]} response [description]
 * @return {[type]}          [description]
 */
function createItem( request, response ) {
    // console.log( request );
    var id = counter += 1,
      // item = request.body;
      item = 'create_'+id;

    console.log( 'Create item', id );
    todoList[id] = item;
    response.writeHead( 201, {
        'Content-Type' : 'text/plain',
        'Location' : '/todo/' + id
    });
}


/**
 * 讀取所有陣列
 * @param  {[type]} request  [description]
 * @param  {[type]} response [description]
 * @return {[type]}          [description]
 */
function readList( request, response ) {
    // console.log( request );
    var item,
        itemList = [],
        listString;

    for ( id in todoList ) {

        if ( !todoList.hasOwnProperty( id ) ) {
            continue;
        }
        item = todoList[ id ];

        if ( typeof item !== 'string' ) {
            continue;
        }

        itemList.push( item );
    }

    console.log( 'Read List: \n', JSON.stringify(
        itemList,
        null,
        '  '
    ));

    listString = itemList.join( '\n' );

    response.writeHead( 200, {
        'Content-Type' : 'text/plain'
    });
    response.end( listString );
}


/**
 * 讀取單一陣列值
 * @param  {[type]} request  [description]
 * @param  {[type]} response [description]
 * @return {[type]}          [description]
 */
function readItem( request, response ) {
    var id = request.params.id,
    item = todoList[ id ];

    if ( typeof item !== 'string' ) {
        console.log( 'Item not found', id );
        response.writeHead( 404 );
        response.end( '\n' );
        return;
    }

    console.log( 'Read item', id, item);

    response.writeHead( 200, {
        'Content-Type' : 'text/plain'
    });

    response.end( item );
}


/**
 * 刪除陣列值
 * @param  {[type]} request  [description]
 * @param  {[type]} response [description]
 * @return {[type]}          [description]
 */
function deleteItem( request, response ) {
    var id = request.params.id;

    if ( typeof todoList[ id ] !== 'string' ) {
        console.log( 'Item not found', id );
        response.writeHead( 404 );
        response.end( '\n' );
        return;
    }

    console.log( 'Delete item', id);

    todoList[ id ] = undefined;
    response.writeHead( 204, {
        'Content-Type' : 'text/plain'
    });
    response.end( '' );
}


/**
 * 更新陣列值
 * @param  {[type]} request  [description]
 * @param  {[type]} response [description]
 * @return {[type]}          [description]
 */
function updateItem( request, response ){
    var id = request.params.id,
        // item = request.body;
        item = 'update_creat_'+id;

    if ( typeof todoList[ id ] !== 'string' ) {
        console.log( 'Item not found', id );
        response.writeHead( 404 );
        response.end( '\n' );
        return;
    }

    console.log( 'Update item', id, item );

    todoList[ id ] = item;
    response.writeHead( 201, {
        'Content-Type' : 'text/plain',
        'Location' : '/todo/' + id
    });
    response.end( item );
}

exports.createItem = createItem;
exports.readList = readList;
exports.readItem = readItem;
exports.deleteItem = deleteItem;
exports.updateItem = updateItem;