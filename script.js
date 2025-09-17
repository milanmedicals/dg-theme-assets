document.addEventListener("DOMContentLoaded", function() {
    const ticker = document.getElementById("headerTicker");
    if (!ticker || !window.pharma_ticker) return;
    const { products, countries, templates } = window.pharma_ticker;
    if (!products.length) return;
    const updatePool = [];
    let recentMessages = [];

    // Precompute initial pool
    (function() {
        const selectedProducts = products.sort(() => Math.random() - 0.5).slice(0, Math.min(8, products.length));
        const selectedCountries = countries.sort(() => Math.random() - 0.5).slice(0, 6);
        selectedProducts.forEach(prod => {
            selectedCountries.forEach(country => {
                const template = templates[Math.floor(Math.random() * templates.length)];
                let message = template.replace("{product}", prod.name).replace("{country}", country);
                if (message.length > 80) message = message.substring(0, 77) + "...";
                if (!recentMessages.includes(message)) updatePool.push(message);
            });
        });
        updatePool.sort(() => Math.random() - 0.5);
    })();

    function getNextUpdate() {
        if (updatePool.length < 5) {
            const selectedProducts = products.sort(() => Math.random() - 0.5).slice(0, Math.min(8, products.length));
            const selectedCountries = countries.sort(() => Math.random() - 0.5).slice(0, 6);
            selectedProducts.forEach(prod => {
                selectedCountries.forEach(country => {
                    const template = templates[Math.floor(Math.random() * templates.length)];
                    let message = template.replace("{product}", prod.name).replace("{country}", country);
                    if (message.length > 80) message = message.substring(0, 77) + "...";
                    if (!recentMessages.includes(message)) updatePool.push(message);
                });
            });
            updatePool.sort(() => Math.random() - 0.5);
        }
        const message = updatePool.shift();
        recentMessages.push(message);
        if (recentMessages.length > 10) recentMessages.shift();
        return message;
    }

    function updateText() {
        ticker.style.opacity = 0;
        setTimeout(() => {
            ticker.textContent = getNextUpdate();
            ticker.style.opacity = 1;
            requestAnimationFrame(() => setTimeout(updateText, 3000 + Math.random() * 2000));
        }, 300);
    }

    updateText();
});

// Existing AJAX search logic (if any) remains unchanged
