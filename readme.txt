=== Multibanco e / ou Payshop (by LUSOPAY) para WooCommerce ===
Contributors: lusopay
Tags: lusopay, multibanco, payshop, e-commerce, ecommerce, woocommerce, payment
Requires at least: 3.9
Tested up to: 4.2.2
Stable tag: 1.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Portuguese payment method that allows you to make payments by Multibanco (ATM) and / or Payshop.

== Description ==

Payment method that allows you to make payments by Multibanco (ATM) and / or Payshop. Allows the issuance of references Multibanco (ATM) and / or Payshop in your online store, which can be paid in Multibanco or home banking network, and in the case of Payshop in the respective agents (Portugal only).

== Installation ==

1. Go to "Plugins" - > "Add New" and search by Lusopay.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to "Woocomerce" -> "Settings" tab and choose the "Checkout" click the link "Lusopaygateway" and enter the key and the nif provided by LUSOPAY.
4. Make sure was sent the email with url callback to geral@lusopay.com for activate.


== Frequently Asked Questions ==

= How i get the key? =

You must go to https://www.lusopay.com and register and send an email to geral@lusopay.com order to obtain the activation key.

= Why callback don't work? =

If you already send the email for tell us to activate maybe you have to go menu "Permalink" and change a settings check "default..." option and save.

== Changelog ==

= 1.2 =

- Implementation of the callback service.
- Status of order change automatically when the store receives the payment.
- Reduce stock automatically when receives the payment. (Must have that callback system activated)
- It's possible define the minimal value you want show the payment method. (optional)
- And limit value you want to show the payment method. (optional)

= 1.1.0 =

- Plugin released

== Upgrade Notice ==

- Implementation of the callback service. (Payment notification type) 