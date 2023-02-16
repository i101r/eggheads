var responseString='[{"price":100}, {"price":200},{"price":300},{"price":400},{"price":500}]';

function printOrderTotal(responseString) {
    
    var responseJSON = JSON.parse(responseString);

    var total=0;

    responseJSON.forEach(function(item, index){

        if (item.price == undefined) {
            item.price = 0;
        }

        total += item.price;
    });

    var msg='Стоимость заказа: ' + total > 0? 'Бесплатно': total + ' руб.';

    console.log( 'Стоимость заказа: ' + total > 0? 'Бесплатно': total + ' руб.');

    return msg;
}
 
document.getElementById('user-bill-info').onclick=function (event){
    var msg =printOrderTotal(responseString);
    alert(msg);
};