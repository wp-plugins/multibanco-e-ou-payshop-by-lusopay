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

(PORTUGUÊS)
Método de pagamento que permite efetuar pagamentos Multibanco e / ou Payshop. Permite gerar referências Multibanco e / ou Payshop na sua loja online, pode ser paga numa Caixa Automática ou no home banking, e no caso da Payshop nos repectivos agentes. 

(ENGILSH)
Payment method that allows you to make payments by Multibanco (ATM) and / or Payshop. Allows the issuance of references Multibanco (ATM) and / or Payshop in your online store, which can be paid in Multibanco or home banking network, and in the case of Payshop in the respective agents (Portugal only).

== Installation ==

English below

(PORTUGUÊS)
1. Vai a "Plugins" - > "Adicionar Novo" e procura por Lusopay.
2. Ative o plugin.
3. Vai a "Woocommerce"->"Configurações" escolhe a aba "Finalizar compras" clica na hiperligação "Pagamentos por Multibanco e / ou Payshop (LUSOPAY)" e coloque a chave e o nif fornecidos pela LUSOPAY.
4. Tenha a certeza que enviou o emial a pedir a activação do sistema callback para geral@lusopay.com (Instruções na página de configurações do plugin).

(ENGLISH)
1. Go to "Plugins" - > "Add New" and search by Lusopay.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to "Woocomerce" -> "Settings" tab and choose the "Checkout" click the link "Pagamentos por Multibanco e / ou Payshop (LUSOPAY)" and enter the key and the nif provided by LUSOPAY.
4. Make sure was sent the email with url callback to geral@lusopay.com for activate.


== Frequently Asked Questions ==

English below

(PORTUGUÊS)
= Como é que obtenho a chave de activação? =

Tem que ir a https://www.lusopay.com e registar-se e enviar um email a pedir o serviço que quer para depois enviarmos a chave.

= O que é o sistema callback? =

O sistema callback é um tipo de notificações dos pagamentos através de um POST, quando o cliente pagar uma encomenda através das referências Multibanco ou Payshop a loja vai automaticamente mudar o estado da encomenda para 

"Confirmado pagamento" e enviar um email dessa confirmação. Com isso o dono da loja não vai ter que estar sempre a verificar a caixa de emails.

= Porque é que o callback não funciona? =

Se já comunicou para nós a informar para activar o callback, talvez tenha que ir a "Opções" escolher "Ligações permanentes" e mudar a opções para predifinição.

(ENGLISH)
= How i get the key? =

You must go to https://www.lusopay.com and register and send an email to geral@lusopay.com order to obtain the activation key.

= What is callback? =

Callback is a payment notification type trough a simple POST, when a client pay an order by Multibanco (ATM) or Payshop references the store updates automatically the order state to "Payment Confirmed" and sends an email his confirmation. Also the store owner doesn't need check the emails boxes to see if the client payed.

= Why callback don't work? =

If you already send the email for tell us to activate, maybe you have to go menu "Settings" choose "Permalink" and change to "default" option and save.


== Changelog ==

English below

(PORTUGUÊS)
= 1.2 =

- Implementação do sistema callback (tipo notificação de pagamento).
- O estado muda automáticamente após um pagamento.
- Reduz o stock automaticamente quando recebe um pagamento. (É necessário ter o callback activo)
- É possível definir um valor mínimo para que apareça o método de pagamento. (opcional)
- E definir um limite para o qual o método de pagamento apareça.

= 1.1.0 =

- Publicação do plugin

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

(PORTUGUÊS)
- Implementação do sistema callback. (Tipo de notificação do pagamento)

(ENGLISH)
- Implementation of the callback service. (Payment notification type) 