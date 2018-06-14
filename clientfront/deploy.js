var FtpDeploy = require('ftp-deploy');
var ftpDeploy = new FtpDeploy();
 
var config = {
    user: "projectEHD55familySWcars",                   // NOTE that this was username in 1.x 
    password: "YW4C%S%4s",           // optional, prompted if none given
    host: "ftp://cars.55soft.net",
    port: 21,
    localRoot: __dirname + '/',
    remoteRoot: '/',
    // include: ['*', '**/*'],      // this would upload everything except dot files
    include: ['dist/*'],
    exclude: ['dist/**/*.map'],     // e.g. exclude sourcemaps
    deleteRoot: true                // delete existing files at destination before uploading
}
 
// use with promises
ftpDeploy.deploy(config)
    .then(res => console.log('finished'))
    .catch(err => console.log(err))
    
// use with callback
ftpDeploy.deploy(config, function(err) {
    if (err) console.log(err)
    else console.log('finished');
});