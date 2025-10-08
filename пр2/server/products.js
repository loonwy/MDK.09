import { products } from "./mockDB.js"

export function loadProducts() {
    return products;
}

export function getProductById(id) {
    return products.find(p => p.id === parseInt(id));
}

export function renderProductCard(product) {
    return `
    <div class="card" data-id="${product.id}">
      <img src="${product.image}" alt="${product.name}">
      <h3>${product.name}</h3>
      <p>${product.description}</p>
      <span class="price">${product.price} ₽</span>
    </div>
  `;
}

export function renderProductList(container) {
    const products = loadProducts();
    container.innerHTML = products.map(renderProductCard).join('');
   
    container.querySelectorAll('.card').forEach(card => {
        card.addEventListener('click', () => {
            const productId = card.dataset.id;
            window.location.href = `../html/product.html?id=${productId}`; 
        });
    });
}


export function renderProductPage(product) {
    if (!product) return null;
    
    return `
        <div class="product-details">
            <div class="product-image">
                <img src="${product.image}" alt="${product.name}">
            </div>
            <div class="product-info">
                <h1>${product.name}</h1>
                <p class="description">${product.description}</p>
                <div class="price">${product.price} ₽</div>
                <button class="add-to-cart" data-id="${product.id}">Добавить в корзину</button>
            </div>
        </div>
    `;
}