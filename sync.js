//nodejs script that downloands and git pushes all changes from SFTP sites.

//var Git = require("nodegit");
var Client = require('ssh2').Client;
var sqlite3 = require('sqlite3').verbose();
var fs = require('fs');
var db = new sqlite3.Database('sites.db');
const punycode = require('punycode');

var downloadR = function(sftp, remoteDir, localDir, depth) {
  return new Promise(function (resolve, reject)  {
    sftp.readdir(remoteDir, function(err, list) {
      if (err) return reject(err);
      var promises = [];
      for(item of list) {
        var remotePath = remoteDir+item.filename;
        var is_dir = item.attrs.mode.toString().startsWith("16");
        if(is_dir) {
          console.log('DIR: '+remotePath)
          var promise = (function(rPath) {
            return new Promise(function(res, rej) {
              fs.mkdir(localDir+rPath, function () {
                downloadR(sftp, rPath+'/', localDir, depth+1).then(res).catch(rej);
              })
            });
          })(remotePath)
          promises.push(promise);
        } else {
          //imediate function closure to scope rPath correctly.
          var dPromise = (function(rPath, rStat) {
              return new Promise(function(res, rej) {
                fs.stat(localDir+rPath, function(err, stat) {
                  //err means file does not exist locally
                  //only download if file size is different.
                  if(err || stat.size != rStat.size) {
                    sftp.fastGet(rPath, localDir+rPath, function(err, result, result2) {
                      if (err) return rej(err);
                      console.log('FILE: '+rPath);
                      res(rPath);
                    });
                  } else {
                    console.log('SAME FILE: '+rPath);
                    res();
                  }
                })
              });
            })(remotePath, item.attrs);
          promises.push(dPromise);
        }
      }
      Promise.all(promises).then(resolve).catch(reject);
    })
  })
}


db.serialize(function() {
  db.each("SELECT * FROM sites", function(err, row) {
    var conn = new Client();
    conn.on('ready', function() {
      console.log('Client :: ready');
      conn.sftp(function(err, sftp) {
        if (err) throw err;
        var downloadDir = './downloads/'+punycode.encode(row.host);
        fs.mkdir(downloadDir, function () {
          downloadR(sftp, '/', downloadDir, 0).then(function () {
            conn.end();
          });
        })
      });
    }).connect({
      host: row.host,
      port: row.port,
      username: row.user,
      password: row.password
    });
  });
});

db.close();
