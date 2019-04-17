var Web3 = require('web3');
//console.log("FRIST = ",web3) ;
var web3 ;
var mysql      = require('mysql');
var connection = mysql.createConnection({
  host     : '18.188.34.49',
  user     : 'bit_user',
  password : 'test123',
  database : 'bitexchange_cash'
});
 
connection.connect();
function twoDigits(d) {
    if(0 <= d && d < 10) return "0" + d.toString();
    if(-10 < d && d < 0) return "-0" + (-1*d).toString();
    return d.toString();
}
 
Date.prototype.toMysqlFormat = function() {
    return this.getUTCFullYear() + "-" + twoDigits(1 + this.getUTCMonth()) + "-" + twoDigits(this.getUTCDate()) + " " + twoDigits(this.getHours()) + ":" + twoDigits(this.getUTCMinutes()) + ":" + twoDigits(this.getUTCSeconds());
};
if(web3 !== undefined){
    web3 = new Web3(web3.currentProvider) ;
}else{
    web3 = new Web3(new Web3.providers.WebsocketProvider("ws://127.0.0.1:8546")) ;
}
web3.eth.getAccounts().then(console.log) ;
// web3.eth.subscribe('logs',{"topics":[null]},function(error,result){
//     if(!error){
//         console.log('result = ',result) ;
//     }else{
//         console.log("ERROR= ",error) ;
//     }
// }).on("data", function(blockHeader){
//     // will return the block number.
//    console.log(blockHeader);
// });

web3.eth.subscribe('newBlockHeaders',function(error,result){
    if(!error){
        //console.log('result = ',result) ;
    }else{
        console.log("ERROR= ",error) ;
    }
}).on("data", function(blockHeader){
    // will return the block number.
   console.log(blockHeader);
   var blockNumber = blockHeader.number ;
   web3.eth.getBlock(blockNumber,true, function(error, result){
        if(!error && result !== null && result){
            console.log("RESULT WITH TRANSACTIONS = ",result) ;
            var transactions = result.transactions ;
            transactions.forEach((transaction)=>{
                var transaction_id = transaction.hash ;
                var amount  = transaction.value ;
                var date = new Date() ;
                var fromAddress = transaction.from ;
                var toAddress = transaction.to ;
                var blockNumber = transaction.blockNumber ;
                var findQuery = "SELECT * from bitcoin_addresses WHERE address = " ;
                findQuery = findQuery+ "'"+toAddress+"'" ;
                console.log("find query = ",findQuery) ;
                connection.query(findQuery ,function(error, results) {
                    if (error) throw error;
                    console.log("RESULTS = ", results )
                    if(results && results.length > 0){
                        var query = "INSERT INTO ethereum_txn (transaction_id, amount, date, fromAddress, toAddress, blockNumber) VALUES (" ;
                        query = query+"'"+ transaction_id +"'," ;
                        amount = web3.utils.fromWei(amount) ;
                        console.log("AMOUNT = ",amount) ;
                        query = query+ amount +"," ;
                        query = query+"'"+ date.toMysqlFormat() +"'," ;
                        query = query+"'"+ fromAddress +"'," ;
                        query = query+"'"+ toAddress +"'," ;
                        query = query+"'"+ blockNumber +"'" ;
                        query = query+")" ;
                        connection.query(query ,function(error, result) {
                            if (error) throw error;
                            console.log('The solution is: ', result);
                        });
                    }else{
                        console.log("NO RESULTS FOUND") ;
                    }
                });
                
            })
        }else{
            console.log(error) ;
        }
   })
});



 
