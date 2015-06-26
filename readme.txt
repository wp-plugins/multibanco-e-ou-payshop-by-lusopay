=== Multibanco e / ou Payshop (by LUSOPAY) para WooCommerce ===
Contributors: lusopay
Tags: lusopay, multibanco, payshop, e-commerce, ecommerce, woocommerce, payment
Requires at least: 3.9
Tested up to: 4.2.2
Stable tag: 1.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Portuguese payment method that allows you to make payments by Multibanco (ATM) and / or Payshop.

== Description ==

English below

(PORTUGU&Ecirc;S)
M&eacute;todo de pagamento que permite efetuar pagamentos Multibanco e / ou Payshop. Permite gerar refer&ecirc;ncias Multibanco e / ou Payshop na sua loja online, pode ser paga numa Caixa Autom&aacute;tica ou no home banking, e no caso da Payshop nos repectivos agentes. Este plugin permite, de forma autom&aacute;tica e em tempo real, alterar os estados das encomendas para pagos no preciso momento em que o cliente paga a refer&ecirc;ncia, bem como actualiza automaticamente o stock dos produtos. Desta forma, com este plugin, para al&eacute;m de permitir enviar os produtos de forma mais c&eacute;lere, reduz o trabalho administrativo que tem para gerir o seu neg&oacute;cio, ao mesmo tempo que permite aos seus clientes pagar de uma forma segura, confort&aacute;vel e com a qual est&atilde;o familiarizados.  

(ENGILSH)
Payment method that allows you to make payments by Multibanco (ATM) and / or Payshop. Allows the issuance of references Multibanco (ATM) and / or Payshop in your online store, which can be paid in Multibanco or home banking network, and in the case of Payshop in the respective agents. This plugin, automatically and in real time, changes the status of orders to paid in the right moment of the payment made by the customer. At the same time it's changes the stock of products. This way, with this plugin, you can send your orders faster than usual, with less administrative work to manage your business and, at the same time, allow your customers to pay in a safe and confortably way.  

== Installation ==

English below

(PORTUGU&Ecirc;S)
1. Vai a "Plugins" - > "Adicionar Novo" e procura por Lusopay.
2. Ative o plugin.
3. Vai a "Woocommerce"->"Configura&ccedil;&otilde;es" escolhe a aba "Finalizar compras" clica na hiperliga&ccedil;&atilde;o "Pagamentos por Multibanco e / ou Payshop (LUSOPAY)" e coloque a chave e o nif fornecidos pela LUSOPAY.
4. Tenha a certeza que enviou o email a pedir a activa&ccedil;&atilde;o do sistema callback para geral@lusopay.com (Instru&ccedil;&otilde;es na p&aacute;gina de configura&ccedil;&otilde;es do plugin).

(ENGLISH)
1. Go to "Plugins" - > "Add New" and search by Lusopay.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to "Woocomerce" -> "Settings" tab and choose the "Checkout". Click the link "Pagamentos por Multibanco e / ou Payshop (LUSOPAY)" and enter the key and the nif provided by LUSOPAY.
4. Make sure that you sent an email with url callback to geral@lusopay.com to allow us to activate callback system.


== Frequently Asked Questions ==

English below

(PORTUGU&Ecirc;S)
= Como &eacute; que obtenho a chave de activa&ccedil;&atilde;o? =

Tem que ir a https://www.lusopay.com e registar-se e enviar um email a pedir o servi&ccedil;o que quer para depois enviarmos a chave.

= O que &eacute; o sistema callback? =

O sistema callback &eacute; um tipo de notifica&ccedil;&otilde;es dos pagamentos atrav&eacute;s de um POST, quando o cliente pagar uma encomenda atrav&eacute;s das refer&ecirc;ncias Multibanco ou Payshop a loja vai automaticamente mudar o estado da encomenda para 

"Confirmado pagamento" e enviar um email dessa confirma&ccedil;&atilde;o. Com isso o dono da loja n&atilde;o vai ter que estar sempre a verificar a caixa de emails.

= Porque &eacute; que o callback n&atilde;o funciona? =

Se j&aacute; comunicou para n&oacute;s a informar para activar o callback, talvez tenha que ir a "Op&ccedil;&otilde;es" escolher "Liga&ccedil;&otilde;es permanentes" e mudar a op&ccedil;&otilde;es para predifini&ccedil;&atilde;o.

(ENGLISH)
= How do I get the key? =

You must go to https://www.lusopay.com, register and send an email to geral@lusopay.com in order to obtain the activation key.

= What is callback? =

Callback is a payment notification type through a simple POST, when a client pays an order by Multibanco (ATM) or Payshop references the online store updates automatically the order state to "Confirmado pagamento" (that means Payment Confirmed) and sends an email informing this status change. Also the store owner doesn't need to check his email boxes to see if the client paid.

= Why callback doesn't work? =

If you already sent the email to tell us to activate callback system, probably you will need to go to menu "Settings" and choose "Permalink" to change it to "default" option and save.


== Changelog ==

English below

(PORTUGU&Ecirc;S)
= 1.2.1 =

- A imagem no checkout n&atilde;o aparecia.

= 1.2 =

- Implementa&ccedil;&atilde;o do sistema callback (tipo notifica&ccedil;&atilde;o de pagamento).
- O estado muda autom&aacute;ticamente ap&oacute;s um pagamento.
- Reduz o stock automaticamente quando recebe um pagamento. (&Eacute; necess&aacute;rio ter o callback activo)
- &Eacute; poss&iacute;vel definir um valor m&iacute;nimo para que apare&ccedil;a o m&eacute;todo de pagamento. (opcional)
- E definir um limite para o qual o m&eacute;todo de pagamento apare&ccedil;a.

= 1.1.0 =

- Publica&ccedil;&atilde;o do plugin

(ENGLISH)

= 1.2.1 =

- Fix image in Checkout.

= 1.2 =

- Implementation of the callback system.
- Status of order change automatically when the store receives the payment.
- Reduce stock automatically when receives the payment. (Must have that callback system activated)
- It's possible to specify the minimum amount of the order to show the payment method. (optional)
- And limit the maximum amount of the order to show the payment method. (optional)

= 1.1.0 =

- Plugin released

== Upgrade Notice ==

English below

(PORTUGU&Ecirc;S)
- Implementa&ccedil;&atilde;o do sistema callback. (Tipo de notifica&ccedil;&atilde;o do pagamento)

(ENGLISH)
- Implementation of the callback service. (Payment notification type) 