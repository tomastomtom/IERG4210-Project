function sanitizer(str) {
  str = str.replace("<", " ");
  str = str.replace(">", " ");
  str = str.replace("/", " ");
  str = str.replace("=", " ");
  return str;
}

var shoppingCartList, totalPrice;
var subTotal = document.getElementById("subTotalAmount");


if (!localStorage.hasOwnProperty('shoppingCartList')) {
  shoppingCartList = {};
  localStorage.setItem('shoppingCartList', JSON.stringify(shoppingCartList));
}
if (!localStorage.hasOwnProperty('totalPrice')) {
  totalPrice = 0.0;
  subTotal.innerHTML = '$' + totalPrice;
  localStorage.setItem('totalPrice', String(totalPrice));
} else {
  totalPrice = Number(localStorage.getItem("totalPrice"));
  subTotal.innerHTML = '$' + totalPrice;
}

function cartItem(pid, amount, price, name) {
  this.pid = pid;
  this.amount = amount;
  this.price = price;
  this.name = name;
}

updateCart();


function ajax(pid) {
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
    if (xhr.readyState == 4 && xhr.status == 200) {
      prod = JSON.parse(xhr.responseText);
      console.log(prod);
      totalPrice = Number(localStorage.getItem("totalPrice"));
      totalPrice += Number(prod['price']);
      localStorage.setItem('totalPrice', String(totalPrice));
      shoppingCartList = JSON.parse(localStorage.getItem('shoppingCartList'));
      console.log(localStorage);
      if (shoppingCartList.hasOwnProperty(pid)) {
        shoppingCartList[pid]['amount'] += 1;
      } else {
        shoppingCartList[pid] = new cartItem(prod['pid'], 1, prod['price'], prod['name']);
      }
      localStorage.setItem('shoppingCartList', JSON.stringify(shoppingCartList));
      console.log(shoppingCartList);
      updateCart();
      subTotal.innerHTML = '$' + totalPrice;
    }
  };
  xhr.open('GET', 'list-process.php?action=prod_fetchOneJSON&pid=' + pid);
  xhr.send();
}

function updateCart() {
  var tmpStr = "";
  var shoppingCartDiv = document.getElementById('itemContainer');
  shoppingCartDiv.innerHTML = "";

  if (!localStorage.hasOwnProperty('shoppingCartList')) {
    shoppingCartList = {};
    localStorage.setItem('shoppingCartList', JSON.stringify(shoppingCartList));
  } else {
    var tmpStr = localStorage.getItem('shoppingCartList');
    tmpStr = sanitizer(tmpStr);
    localStorage.setItem('shoppingCartList', tmpStr);
    shoppingCartList = JSON.parse(localStorage.getItem('shoppingCartList'));
  }
  if (!localStorage.hasOwnProperty('totalPrice')) {
    totalPrice = 0;
    localStorage.setItem('totalPrice', String(totalPrice));
  } else {
    var tmpStr = localStorage.getItem('totalPrice');
    tmpStr = sanitizer(tmpStr);
    localStorage.setItem('totalPrice', tmpStr);
    totalPrice = Number(localStorage.getItem('totalPrice'));
  }
  console.log(shoppingCartList);

  for (var pid in shoppingCartList) {
    shoppingCartDiv.innerHTML +=
      '<p class="cartItem"><span class="cartItemsPic"><img src="img/product/thumbnail/' + shoppingCartList[pid]['pid'] + 'Thumb.jpg"></span><span class="cartItemsName">' + shoppingCartList[pid]['name'] + '</span> <span class="cartItemPrice"> <input type="number" value="' + shoppingCartList[pid]['amount'] + '" min="0" class="cartItemNumber" data-pid ="' + shoppingCartList[pid]['pid'] + '">' + shoppingCartList[pid]['price'] + '</span></p>';
  }
  var amountInput = document.querySelectorAll('.cartItemNumber');
  for (var i = 0; i < amountInput.length; i++) {
    amountInput[i].addEventListener("change", function() {
      var Value = Number(this.value);
      var Pid = this.dataset.pid;
      totalPrice = 0;
      for (var j = 0; j < amountInput.length; j++) {
        totalPrice += shoppingCartList[amountInput[j].dataset.pid]['price'] * Number(amountInput[j].value);
        shoppingCartList[Pid]['amount'] = Value;
        localStorage.setItem('shoppingCartList', JSON.stringify(shoppingCartList));
      }
      localStorage.setItem('totalPrice', String(totalPrice));
      if (Value == 0) {
        delete shoppingCartList[Pid];
        localStorage.setItem('shoppingCartList', JSON.stringify(shoppingCartList));
        updateCart();
      }
      subTotal.innerHTML = '$' + totalPrice;
    })
  }
}


if (document.querySelector('.addToCart')) {
  var addingButtons = document.querySelectorAll('.addToCart');
  for (var k = 0; k < addingButtons.length; k++) {
    addingButtons[k].addEventListener('click', function() {
      var pid = Number(this.dataset.pid);
      ajax(pid);
    });
  }
}

if (document.querySelector('.productPageCart')) {
  var addingButtonsProduct = document.querySelector('.productPageCart');
  var pid = addingButtonsProduct.dataset.pid;
  addingButtonsProduct.addEventListener("click", function() {
    ajax(pid);
  });
}