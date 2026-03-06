

## Problem Analysis

I thoroughly reviewed all the code. The `addToCartXHR` function in `footer.php` and `cart-add-form.php` are correctly implemented. The toast system (`showCartToastGlobal`) is also in place. However, there are two critical bugs preventing proper operation:

### Bug 1: Cart badge not found when cart is empty
In `product.php` (which has its own header, not shared `header.php`), the `.cart-count-badge` element is only rendered when `$header_cart_count > 0`. When the cart is empty, the badge HTML doesn't exist in the DOM, so JavaScript can't find or update it.

The product detail page's `ajaxAddToCart` handles this by dynamically creating the badge if it doesn't exist (lines 1547-1554), but `footer.php`'s `addToCartXHR` does NOT have this fallback — it just silently fails to update.

**Fix**: In `footer.php`'s `addToCartXHR` function, add the same badge-creation fallback that `product.php` uses.

### Bug 2: product.php header badge inconsistency
`header.php` always renders the badge with `display:none` when empty. But `product.php` has its own duplicate header code that conditionally renders the badge (lines 762-764). This needs to match `header.php`'s approach.

**Fix**: Update `product.php` lines 762-764 to always render the badge (hidden when count is 0), matching `header.php`.

## Plan

### 1. Fix `footer.php` — Add badge creation fallback to `addToCartXHR`
In the `addToCartXHR` function (around line 1066), after attempting to update existing badges, add logic to create a badge element if none exist — exactly like `product.php`'s `ajaxAddToCart` does:

```javascript
// After updating existing badges
if(badges.length === 0) {
    var cartLink = document.querySelector('.cart-trigger');
    if(cartLink) {
        var badge = document.createElement('span');
        badge.className = 'cart-count-badge';
        badge.textContent = res.cart_count;
        cartLink.appendChild(badge);
    }
}
// Show hidden badges
for(var i=0; i<badges.length; i++) {
    badges[i].style.display = '';
}
```

### 2. Fix `product.php` — Always render badge element
Change lines 762-764 from conditional rendering to always-present with `display:none`:
```php
<span class="cart-count-badge" style="<?php echo $header_cart_count > 0 ? '' : 'display:none;'; ?>"><?php echo $header_cart_count; ?></span>
```

These are the only two changes needed. No other files need modification. The toast, XHR, backend endpoint, and product card button are all already working correctly.

