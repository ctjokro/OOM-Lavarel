
 <?php

            use Moltin\Cart\Cart;
            use Moltin\Cart\Storage\CartSession;
            use Moltin\Cart\Identifier\Cookie;

$cart = new Cart(new CartSession, new Cookie);
            ?> 
{{html_entity_decode(HTML::link('order/confirm', '<i class="fa fa-shopping-cart" aria-hidden="true"></i>'.$cart->totalItems())); }}