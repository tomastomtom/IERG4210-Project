// 1)json stringtify the localStorage
// 2)ajax post request to php
// 3)php dealing with the post json
// 4)ajax php return the required info
// 5)send the damn form
var orderId = 'testing';
var orderDigest = 'testing';
var orderEmail = 'testing';
var shoppingCartList = JSON.parse(localStorage.getItem('shoppingCartList'));
var orderPrice = [];

function ajax_request() {
  var dataObject = {
    pid: [],
    amount: []
  };
  for (var pid in shoppingCartList) {
    dataObject['pid'].push(Number(shoppingCartList[pid]['pid']));
    dataObject['amount'].push(Number(shoppingCartList[pid]['amount']));
  }
  var data = JSON.stringify(dataObject);
  console.log(data);
  var xhr = new XMLHttpRequest();
  xhr.open("POST", 'checkout-process.php?action=insertOrder', false);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      console.log('response:' + xhr.responseText);
      var xhrResult = JSON.parse(xhr.responseText);

      orderId = xhrResult['id'];
      console.log(orderId);
      orderDigest = xhrResult['digest'];
      console.log(orderDigest);
      orderEmail = xhrResult['email'];
      console.log(orderEmail);
      orderPrice = xhrResult['price'];
      console.log(orderPrice);
    }
  }
  xhr.send("order=" + data);
}


function cartSubmit(event) {
  event.preventDefault();
  console.log("testing");
  ajax_request();
  var newForm = document.createElement('form');
  newForm.setAttribute('id', 'newCartList');
  newForm.setAttribute('action', 'https://www.sandbox.paypal.com/cgi-bin/webscr');
  newForm.setAttribute('method', 'post');

  var cmd = document.createElement('input');
  cmd.setAttribute('type', 'hidden');
  cmd.setAttribute('name', 'cmd');
  cmd.setAttribute('value', '_cart');
  newForm.appendChild(cmd);

  var upload = document.createElement('input');
  upload.setAttribute('type', 'hidden');
  upload.setAttribute('name', 'upload');
  upload.setAttribute('value', '1');
  newForm.appendChild(upload);

  var business = document.createElement('input');
  business.setAttribute('type', 'hidden');
  business.setAttribute('name', 'business');
  business.setAttribute('value', orderEmail);
  newForm.appendChild(business);


  var currency_code = document.createElement('input');
  currency_code.setAttribute('type', 'hidden');
  currency_code.setAttribute('name', 'currency_code');
  currency_code.setAttribute('value', 'HKD');
  newForm.appendChild(currency_code);

  var charset = document.createElement('input');
  charset.setAttribute('type', 'hidden');
  charset.setAttribute('name', 'charset');
  charset.setAttribute('value', 'utf-8');
  newForm.appendChild(charset);

  var custom = document.createElement('input');
  custom.setAttribute('type', 'hidden');
  custom.setAttribute('name', 'custom');
  custom.setAttribute('value', orderDigest);
  newForm.appendChild(custom);


  var invoice = document.createElement('input');
  invoice.setAttribute('type', 'hidden');
  invoice.setAttribute('name', 'invoice');
  invoice.setAttribute('value', orderId);
  newForm.appendChild(invoice);


  var i = 1;
  for (var pid in shoppingCartList) {
    var itemName = document.createElement('input');
    itemName.setAttribute('type', 'hidden');
    itemName.setAttribute('name', 'item_name_' + i);
    itemName.setAttribute('value', shoppingCartList[pid]['name']);
    newForm.appendChild(itemName);

    var itemNumber = document.createElement('input');
    itemNumber.setAttribute('type', 'hidden');
    itemNumber.setAttribute('name', 'item_number_' + i);
    itemNumber.setAttribute('value', shoppingCartList[pid]['pid']);
    newForm.appendChild(itemNumber);

    var itemPrice = document.createElement('input');
    itemPrice.setAttribute('type', 'hidden');
    itemPrice.setAttribute('name', 'amount_' + i);
    itemPrice.setAttribute('value', orderPrice[i - 1]);
    newForm.appendChild(itemPrice);
    var itemQuantity = document.createElement('input');
    itemQuantity.setAttribute('type', 'hidden');
    itemQuantity.setAttribute('name', 'quantity_' + i);
    itemQuantity.setAttribute('value', shoppingCartList[pid]['amount']);
    newForm.appendChild(itemQuantity);
    i++;
  }

  localStorage.clear();
  document.body.appendChild(newForm);
  newForm.submit();
}