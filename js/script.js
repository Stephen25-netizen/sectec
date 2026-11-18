// ===================================
// SECTEC NETWORK LTD
// E-COMMERCE JAVASCRIPT
// ===================================


// PRODUCT DATABASE

const products = [

    {
        id: 1,
        name: "SC/APC Fast Connector",
        category: "Fiber Accessories",
        price: 60,
        image: "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600",
        description: "High-quality SC/APC fiber optic fast connector for reliable fiber connections."
    },

    {
        id: 2,
        name: "SC/UPC Fast Connector",
        category: "Fiber Accessories",
        price: 60,
        image: "https://images.unsplash.com/photo-1518770660439-4636190af475?w=600",
        description: "Reliable SC/UPC fast connector for fiber optic networking installations."
    },

    {
        id: 3,
        name: "ATB Loaded SC/UPC",
        category: "Fiber Accessories",
        price: 120,
        image: "https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=600",
        description: "Loaded access terminal box suitable for fiber optic network distribution."
    },

    {
        id: 4,
        name: "Patch Cord LC UPC/SC APC 2m",
        category: "Fiber Accessories",
        price: 120,
        image: "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600",
        description: "Two-metre simplex fiber optic patch cord."
    },

    {
        id: 5,
        name: "Patch Cord SC UPC/LC APC 2m",
        category: "Fiber Accessories",
        price: 150,
        image: "https://images.unsplash.com/photo-1518770660439-4636190af475?w=600",
        description: "High-quality fiber optic patch cable."
    },

    {
        id: 6,
        name: "Wi-Fi Extender",
        category: "Fiber Accessories",
        price: 1500,
        image: "https://images.unsplash.com/photo-1606904825846-647eb07f5be2?w=600",
        description: "Improve wireless network coverage with this Wi-Fi extender."
    },

    {
        id: 7,
        name: "12V 1.5A Adapter",
        category: "Power Backup Systems",
        price: 250,
        image: "https://images.unsplash.com/photo-1625842268584-8f3296236761?w=600",
        description: "12V 1.5A power adapter for compatible electronic devices."
    },

    {
        id: 8,
        name: "ATB Empty 2 Port",
        category: "Fiber Accessories",
        price: 60,
        image: "https://images.unsplash.com/photo-1518770660439-4636190af475?w=600",
        description: "Two-port access terminal box for fiber optic installations."
    },

    {
        id: 9,
        name: "Access Terminal Box Loaded",
        category: "Fiber Accessories",
        price: 250,
        image: "https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=600",
        description: "Access terminal box loaded with tray."
    },

    {
        id: 10,
        name: "Automatic Changeover 2 Pin",
        category: "Power Backup Systems",
        price: 4000,
        image: "https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=600",
        description: "Automatic electrical changeover switch."
    },

    {
        id: 11,
        name: "Automatic Changeover Switch 4 Pin",
        category: "Power Backup Systems",
        price: 6000,
        image: "https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=600",
        description: "Four-pin automatic changeover switch."
    },

    {
        id: 12,
        name: "AC Flood Light Camera WiFi",
        category: "CCTV Cameras",
        price: 4000,
        image: "https://images.unsplash.com/photo-1557597774-9d273605dfa9?w=600",
        description: "Wi-Fi-enabled floodlight camera for security monitoring."
    },

    {
        id: 13,
        name: "Outdoor CCTV Security Camera",
        category: "CCTV Cameras",
        price: 3500,
        image: "https://images.unsplash.com/photo-1557597774-9d273605dfa9?w=600",
        description: "Security camera for outdoor surveillance."
    },

    {
        id: 14,
        name: "Solar Security Light",
        category: "Solar Lights",
        price: 2500,
        image: "https://images.unsplash.com/photo-1509391366360-2e959784a276?w=600",
        description: "Solar-powered outdoor lighting solution."
    },

    {
        id: 15,
        name: "Electric Water Heater",
        category: "Water Heaters",
        price: 8500,
        image: "images/heater.png",
        description: "Quality HINET SOLUTIONS electric water heater for household use."
    },
    {
        id: 16,
        name: "Portable Power Battery",
        category: "Power Backup Systems",
        price: 24000, 
        image:"images/power battery.jpg",
        description: "Portable rechargeable power battery for backup electricity and powering electronic devices."
    }
    

    


];


// CART DATA

let cart = JSON.parse(localStorage.getItem("sectecCart")) || [];

let currentCategory = "All";


// FORMAT CURRENCY

function formatPrice(price) {

    return "KSh " + Number(price).toLocaleString("en-KE");

}


// DISPLAY PRODUCTS

function displayProducts(items = products) {

    const productGrid = document.getElementById("product-grid");

    productGrid.innerHTML = "";

    if (items.length === 0) {

        productGrid.innerHTML = "<p>No products found.</p>";

        return;

    }

    items.forEach(product => {

        productGrid.innerHTML += `

            <div class="product-card">

                <div class="product-image">

                    <span class="product-badge">Available</span>

                    <img src="${product.image}"
                    alt="${product.name}"
                    loading="lazy">

                </div>

                <div class="product-info">

                    <span class="product-category">
                        ${product.category}
                    </span>

                    <h3>${product.name}</h3>

                    <div class="product-price">
                        ${formatPrice(product.price)}
                    </div>

                    <div class="product-actions">

                        <button class="add-cart"
                        onclick="addToCart(${product.id})">

                            <i class="fas fa-shopping-cart"></i>
                            Add to Cart

                        </button>

                        <button class="view-product"
                        onclick="viewProduct(${product.id})">

                            View

                        </button>

                    </div>

                </div>

            </div>

        `;

    });

}


// FILTER PRODUCTS

function filterCategory(category) {

    currentCategory = category;

    let filtered;

    if (category === "All") {

        filtered = products;

    } else {

        filtered = products.filter(product =>
            product.category === category
        );

    }

    displayProducts(filtered);

    document.querySelectorAll(".filter-btn").forEach(button => {

        button.classList.remove("active");

        if (button.textContent.trim() === category ||
            (category === "All" && button.textContent.trim() === "All Products")) {

            button.classList.add("active");

        }

    });

    document.getElementById("products").scrollIntoView({
        behavior: "smooth"
    });

}


// ADD TO CART

function addToCart(id) {

    const product = products.find(item => item.id === id);

    const existing = cart.find(item => item.id === id);

    if (existing) {

        existing.quantity++;

    } else {

        cart.push({

            ...product,

            quantity: 1

        });

    }

    saveCart();

    showToast(product.name + " added to cart!");

}


// SAVE CART

function saveCart() {

    localStorage.setItem("sectecCart", JSON.stringify(cart));

    updateCart();

}


// UPDATE CART

function updateCart() {

    const cartItems = document.getElementById("cart-items");

    const cartCount = document.querySelector(".cart-count");

    const cartTotal = document.getElementById("cart-total");

    cartItems.innerHTML = "";

    let total = 0;

    let count = 0;

    cart.forEach(item => {

        total += item.price * item.quantity;

        count += item.quantity;

        cartItems.innerHTML += `

            <div class="cart-item">

                <img src="${item.image}" alt="${item.name}">

                <div>

                    <h4>${item.name}</h4>

                    <p>${formatPrice(item.price)}</p>

                    <div class="cart-quantity">

                        <button onclick="changeQuantity(${item.id}, -1)">-</button>

                        <span>${item.quantity}</span>

                        <button onclick="changeQuantity(${item.id}, 1)">+</button>

                    </div>

                </div>

                <button class="remove-item"
                onclick="removeFromCart(${item.id})">

                    <i class="fas fa-trash"></i>

                </button>

            </div>

        `;

    });

    if (cart.length === 0) {

        cartItems.innerHTML = "<p>Your shopping cart is empty.</p>";

    }

    cartCount.textContent = count;

    cartTotal.textContent = formatPrice(total);

}


// CHANGE QUANTITY

function changeQuantity(id, change) {

    const item = cart.find(product => product.id === id);

    if (!item) return;

    item.quantity += change;

    if (item.quantity <= 0) {

        cart = cart.filter(product => product.id !== id);

    }

    saveCart();

}


// REMOVE PRODUCT

function removeFromCart(id) {

    cart = cart.filter(item => item.id !== id);

    saveCart();

    showToast("Product removed from cart.");

}


// VIEW PRODUCT DETAILS

function viewProduct(id) {

    const product = products.find(item => item.id === id);

    const modal = document.getElementById("product-modal");

    const details = document.getElementById("modal-details");

    details.innerHTML = `

        <div class="modal-product">

            <img src="${product.image}" alt="${product.name}">

            <div>

                <span class="product-category">
                    ${product.category}
                </span>

                <h2>${product.name}</h2>

                <h3 class="product-price">
                    ${formatPrice(product.price)}
                </h3>

                <p>${product.description}</p>

                <button class="add-cart"
                onclick="addToCart(${product.id}); closeModal();">

                    Add to Cart

                </button>

            </div>

        </div>

    `;

    modal.classList.add("active");

}


// CLOSE MODAL

function closeModal() {

    document.getElementById("product-modal").classList.remove("active");

}


// CHECKOUT

function checkout() {

    if (cart.length === 0) {

        showToast("Your shopping cart is empty.");

        return;

    }

    let message = "Hello SECTEC NETWORK LTD! I would like to order:%0A%0A";

    let total = 0;

    cart.forEach(item => {

        message += `${item.name} - ${item.quantity} x ${formatPrice(item.price)}%0A`;

        total += item.price * item.quantity;

    });

    message += `%0ATotal: ${formatPrice(total)}`;

    message += "%0A%0APlease assist me with placing this order.";

    window.open(
        "https://wa.me/254796444496?text=" + message,
        "_blank"
    );

}


// SEARCH PRODUCTS

function searchProducts() {

    const searchValue = document.getElementById("search-input")
        .value.toLowerCase().trim();

    const results = products.filter(product =>

        product.name.toLowerCase().includes(searchValue) ||

        product.category.toLowerCase().includes(searchValue)

    );

    displayProducts(results);

    document.getElementById("products").scrollIntoView({
        behavior: "smooth"
    });

}


// TOAST MESSAGE

function showToast(message) {

    const toast = document.getElementById("toast");

    toast.textContent = message;

    toast.classList.add("show");

    setTimeout(() => {

        toast.classList.remove("show");

    }, 2500);

}


// OPEN CART

document.getElementById("cart-toggle").addEventListener("click", () => {

    document.getElementById("cart-sidebar").classList.add("active");

    document.getElementById("cart-overlay").classList.add("active");

});


// CLOSE CART

function closeCart() {

    document.getElementById("cart-sidebar").classList.remove("active");

    document.getElementById("cart-overlay").classList.remove("active");

}

document.getElementById("close-cart").addEventListener("click", closeCart);

document.getElementById("cart-overlay").addEventListener("click", closeCart);


// MOBILE MENU

document.getElementById("menu-btn").addEventListener("click", () => {

    document.getElementById("navbar").classList.toggle("active");

});


// CLOSE MOBILE MENU WHEN LINK IS CLICKED

document.querySelectorAll(".navbar a").forEach(link => {

    link.addEventListener("click", () => {

        document.getElementById("navbar").classList.remove("active");

    });

});


// SEARCH BUTTON

document.getElementById("search-toggle").addEventListener("click", () => {

    document.getElementById("search-box").classList.toggle("active");

});

document.getElementById("search-btn").addEventListener("click", searchProducts);

document.getElementById("search-input").addEventListener("keydown", event => {

    if (event.key === "Enter") {

        searchProducts();

    }

});


// CLOSE PRODUCT MODAL

document.getElementById("modal-close").addEventListener("click", closeModal);

document.getElementById("product-modal").addEventListener("click", event => {

    if (event.target.id === "product-modal") {

        closeModal();

    }

});


// NEWSLETTER

document.getElementById("newsletter-form").addEventListener("submit", event => {

    event.preventDefault();

    showToast("Thank you for subscribing!");

    event.target.reset();

});


// INITIALIZE WEBSITE

displayProducts();

updateCart();