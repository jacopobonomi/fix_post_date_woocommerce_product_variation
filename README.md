# Fix 'post_date' in Woocommerce variation product from 'post_date' of father
## Usage:
* Do a backup of your db.
* Copy content of the file fix_post_date.php without tag ``` <?php ?> ```.
* Check your database prefix (default is ``` wp_ ```)
* Paste all in your functions.php of theme.
* Go in a random page of your website, one time.
* Come back to your functions.php and delete the snippet.
* Check the database and enjoy.

### Why i need that:
If you usa a plugin like "Products By Attributes & Variations for WooCommerce" or "WPC Show Single Variations for WooCommerce", and you need to order the products from date, you can use that to fix variation order.

It is really a simple function, but make a backup of the db anyway.
