import { validateForm } from '../server/validation.js';
import { loginUser, registerUser, getCurrentUser, logoutUser } from '../server/auth.js';
import { renderProductList } from '../server/products.js';
import { products } from "../server/mockDB.js";

if (document.querySelector('.login-container')) {
  const form = document.querySelector('form');
  const loginInput = form.querySelector('input[type="text"]'); 
  const passwordInput = form.querySelector('input[type="password"]');

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    
    const login = loginInput.value;
    const password = passwordInput.value;

    const { isValid, loginError, passwordError } = validateForm(login, password);
    if (!isValid) {
      alert(loginError || passwordError);
      return;
    }

    const result = loginUser(login, password);
    if (result.success) {
     
      localStorage.setItem('currentUser', JSON.stringify(result.user));
      alert('Вход успешен!');
      window.location.href = 'index.html';
    } else {
      alert(result.error);
    }
  });
}

if (document.querySelector('.cards')) {
  const container = document.querySelector('.cards');
  renderProductList(container);
}

async function loadPartials() {
  const header = await fetch("other/header.html").then(res => res.text());
  document.getElementById("header").innerHTML = header;

  const footer = await fetch("other/footer.html").then(res => res.text());
  document.getElementById("footer").innerHTML = footer;
}

loadPartials();


if (document.querySelector('.product-page')) {
  const urlParams = new URLSearchParams(window.location.search);
  const productId = urlParams.get('id');
  if (productId) {
    const product = products.find(p => p.id == productId);
    if (product) {
      document.querySelector('.product-page img').src = product.image;
      document.querySelector('.product-info h1').textContent = product.name;
      document.querySelector('.product-info p').textContent = product.description;
      document.querySelector('.product-info .price').textContent = `${product.price} ₽`;

    }
  }
}