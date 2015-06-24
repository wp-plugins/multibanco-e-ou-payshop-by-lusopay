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

English below

(PORTUGU�S)
M�todo de pagamento que permite efetuar pagamentos Multibanco e / ou Payshop. Permite gerar refer�ncias Multibanco e / ou Payshop na sua loja online, pode ser paga numa Caixa Autom�tica ou no home banking, e no caso da Payshop nos repectivos agentes. 

(ENGILSH)
Payment method that allows you to make payments by Multibanco (ATM) and / or Payshop. Allows the issuance of references Multibanco (ATM) and / or Payshop in your online store, which can be paid in Multibanco or home banking network, and in the case of Payshop in the respective agents (Portugal only).

== Installation ==

English below

(PORTUGU�S)
1. Vai a "Plugins" - > "Adicionar Novo" e procura por Lusopay.
2. Ative o plugin.
3. Vai a "Woocommerce"->"Configura��es" escolhe a aba "Finalizar compras" clica na hiperliga��o "Pagamentos por Multibanco e / ou Payshop (LUSOPAY)" e coloque a chave e o nif fornecidos pela LUSOPAY.
4. Tenha a certeza que enviou o emial a pedir a activa��o do sistema callback para geral@lusopay.com (Instru��es na p�gina de configura��es do plugin).

(ENGLISH)
1. Go to "Plugins" - > "Add New" and search by Lusopay.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to "Woocomerce" -> "Settings" tab and choose the "Checkout" click the link "Pagamentos por Multibanco e / ou Payshop (LUSOPAY)" and enter the key and the nif provided by LUSOPAY.
4. Make sure was sent the email with url callback to geral@lusopay.com for activate.


== Frequently Asked Questions ==

English below

(PORTUGU�S)
= Como � que obtenho a chave de activa��o? =

Tem que ir a https://www.lusopay.com e registar-se e enviar um email a pedir o servi�o que quer para depois enviarmos a chave.

= O que � o sistema callback? =

O sistema callback � um tipo de notifica��es dos pagamentos atrav�s de um POST, quando o cliente pagar uma encomenda atrav�s das refer�ncias Multibanco ou Payshop a loja vai automaticamente mudar o estado da encomenda para 

"Confirmado pagamento" e enviar um email dessa confirma��o. Com isso o dono da loja n�o vai ter que estar sempre a verificar a caixa de emails.

= Porque � que o callback n�o funciona? =

Se j� comunicou para n�s a informar para activar o callback, talvez tenha que ir a "Op��es" escolher "Liga��es permanentes" e mudar a op��es para predifini��o.

(ENGLISH)
= How i get the key? =

You must go to https://www.lusopay.com and register and send an email to geral@lusopay.com order to obtain the activation key.

= What is callback? =

Callback is a payment notification type trough a simple POST, when a client pay an order by Multibanco (ATM) or Payshop references the store updates automatically the order state to "Payment Confirmed" and sends an email his confirmation. Also the store owner doesn't need check the emails boxes to see if the client payed.

= Why callback don't work? =

If you already send the email for tell us to activate, maybe you have to go menu "Settings" choose "Permalink" and change to "default" option and save.


== Changelog ==

English below

(PORTUGU�S)
= 1.2 =

- Implementa��o do sistema callback (tipo notifica��o de pagamento).
- O estado muda autom�ticamente ap�s um pagamento.
- Reduz o stock automaticamente quando recebe um pagamento. (� necess�rio ter o callback activo)
- � poss�vel definir um valor m�nimo para que apare�a o m�todo de pagamento. (opcional)
- E definir um limite para o qual o m�todo de pagamento apare�a.

= 1.1.0 =

- Publica��o do plugin

(ENGLISH)

= 1.2 =

- Implementation of the callback service.
- Status of order change automatically when the store receives the payment.
- Reduce stock automatically when receives the payment. (Must have that callback system activated)
- It's possible define the minimal value you want show the payment method. (optional)
- And limit value you want to show the payment method. (optional)

= 1.1.0 =

- Plugin released

== Upgrade Notice ==

English below

(PORTUGU�S)
- Implementa��o do sistema callback. (Tipo de notifica��o do pagamento)

(ENGLISH)
- Implementation of the callback service. (Payment notification type) 