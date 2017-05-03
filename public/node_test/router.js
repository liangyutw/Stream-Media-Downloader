var requestHandlers = require("./requestHandlers.js");



function route(parse, request, response) {
    var pathname = parse.pathname;
    // console.log("About to route a request for " + pathname);
    if (typeof handle[pathname] === 'function') {
        // console.log(re);return false;

        handle[pathname](parse, request, response);

    } else {
        console.log("No request handler found for " + pathname);
        response.writeHead(404, {"Content-Type": "text/plain"});
        response.write("404 Not found");
        response.end();
    }
}

var handle = {}
handle["/"] = requestHandlers.start;
handle["/start"] = requestHandlers.start;
handle["/upload"] = requestHandlers.upload;     //create
handle["/list"] = requestHandlers.file_list;    //read
handle["/rename"] = requestHandlers.rename;     //update
handle["/delete"] = requestHandlers.delete;     //delete

exports.route = route;
exports.handle = handle;
