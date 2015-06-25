<?php
/*
* Plugin Name: WooCommerce LusopayGateway
* Plugin URI: https://www.lusopay.com
* Description: Plugin oficial da LUSOPAY para WooCommerce para Pagamentos por Multibanco e / ou Payshop. Para o poder utilizar tem de efetuar um registo em <a href="https://www.lusopay.com" target="_blank">LUSOPAY</a> para poder utilizar este plugin. Para mais informa&ccedil;&otilde;es de como aderir <a href="https://www.lusopay.com" target="_blank">clique aqui</a>.
* Version: 1.2
* Author: LUSOPAY
* Author URI: https://www.lusopay.com
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Check if WooCommerce is active
 **/
// Get active network plugins - "Stolen" from Novalnet Payment Gateway
function lusopaygateway_active_nw_plugins() {
	if (!is_multisite())
		return false;
	$lusopaygateway_activePlugins = (get_site_option('active_sitewide_plugins')) ? array_keys(get_site_option('active_sitewide_plugins')) : array();
	return $lusopaygateway_activePlugins;
}
if (in_array('woocommerce/woocommerce.php', (array) get_option('active_plugins')) || in_array('woocommerce/woocommerce.php', (array) lusopaygateway_active_nw_plugins())) {

	//Languages
	/*
	add_action('plugins_loaded', 'lusopaygateway_lang');
	function lusopaygateway_lang() {
		load_plugin_textdomain('lusopaygateway', false, dirname(plugin_basename(__FILE__)) . '/lang/');
	}
	*/
	add_action( 'plugins_loaded', 'woocommerce_lusopaygateway_init', 0);
	function woocommerce_lusopaygateway_init() {
		
		if ( ! class_exists( 'WC_Lusopaygateway' ) ) {
			class WC_Lusopaygateway extends WC_Payment_Gateway {
				
				
				public function __construct() {
					global $woocommerce;

					$this->id = 'lusopaygateway';

					//Check version and upgrade

					// Logs
					$this->debug = ($this->settings['debug']=='yes' ? true : false);
					if ($this->debug) $this->log = new WC_Logger();
					$this->debug_email = $this->settings['debug_email'];
					
					$this->version = '1.2';
					$this->upgrade();

					//load_plugin_textdomain('lusopaygateway', false, dirname(plugin_basename(__FILE__)) . '/lang/');
					//$this->icon = WP_PLUGIN_URL."/".plugin_basename( dirname(__FILE__)) . '/images/icon.png';
					$this->icon = plugins_url('images/Logo_Lusopay_MBePayshop52x33px.png', __FILE__);
					$this->has_fields = false;
					$this->method_title = __('Pagamentos por Multibanco e / ou Payshop (LUSOPAY)', 'lusopaygateway');
					$this->secret_key = $this->get_option('secret_key');
					if (trim($this->secret_key)=='') {
						$this->secret_key=md5(home_url().time().rand(0,999));
					}
					$this->notify_url = str_replace( 'https:', 'http:', home_url( '/' ) ).'wc-api/WC_Lusopaygateway/?entidade=«entidade»&referencia=«referencia»&valor=«valor»&chave='.$this->secret_key;

					//Plugin options and settings
					$this->init_form_fields();
					$this->init_settings();
					
					

					//User settings
					$this->title = $this->settings['title'];
					$this->description = $this->settings['description'];
					$this->chave = $this->settings['chave'];
					$this->nif = $this->settings['nif'];
					$this->only_portugal = $this->settings['only_portugal'];
					$this->only_above = $this->settings['only_above'];
					$this->only_bellow = $this->settings['only_bellow'];
					$this->stock_when = $this->settings['stock_when'];
					
					global $wpdb;
					global $mag_db_version;
					$mag_db_version = "1.0";
					
					require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

					$table_name = $wpdb->prefix . "magnimeiosreferences";
					
					$res = $wpdb->get_results("SHOW TABLES LIKE ".$table_name);
					//var_dump($res);
					$charset_collate = $wpdb->get_charset_collate();
					//var_dump($res);
					
					if(!empty($res)){
						$sql = $wpdb->query("ALTER TABLE ".$table_name." ADD COLUMN status VARCHAR(10);");
						
						
						
					}else{
						$sql = $wpdb->query("CREATE TABLE IF NOT EXISTS ".$table_name." (id_order int, refMB VARCHAR(9), refPS VARCHAR(13), value VARCHAR(10), status VARCHAR(10));");
						//require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
						//dbDelta( $sql );
					}

					dbDelta( $sql );


					
					//require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
					//dbDelta( $sql );

					//add_option( "mag_db_version", $mag_db_version );
					
			 
					// Actions and filters
					add_action('woocommerce_update_options_payment_gateways_'.$this->id, array($this, 'process_admin_options'));
					//if (function_exists('icl_object_id') && function_exists('icl_register_string')) add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'register_wpml_strings'));
					//add_action('woocommerce_thankyou_'.$this->id, array($this, 'thankyou'));
					
					add_action('woocommerce_thankyou_lusopaygateway', array(&$this, 'thankyou'));
					add_filter('woocommerce_available_payment_gateways', array($this, 'disable_unless_portugal'));
					add_filter('woocommerce_available_payment_gateways', array($this, 'disable_only_above_or_bellow'));
				 
					// Customer Emails
					add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 2);

					// Payment listener/API hook
					//add_action('woocommerce_api_wc_lusopaygateway', array(&$this, 'callback_handler'));
					add_action('woocommerce_api_'.strtolower(get_class($this)), array(&$this, 'callback'));
					
					
					
				}

				/**
				 * Upgrades (if needed)
				 */
				function upgrade() {
					if ($this->settings['version']<$this->version) {
						//Upgrade
						if ($this->debug) $this->log->add($this->id, 'Upgrade to '.$this->version.' started');
						if ($this->version=='1.0.1') {
							//Only change is to set the version on the database. It's done below
						}
						//Upgrade on the database - Risky?
						$temp=get_option('woocommerce_lusopaygateway_settings','');
						$temp['version']=$this->version;
						update_option('woocommerce_lusopaygateway_settings', $temp);
						if ($this->debug) $this->log->add($this->id, 'Upgrade to '.$this->version.' finished');
					}
				}

				/**
				 * WPML compatibility
				 */
				function register_wpml_strings() {
					$to_register=array(
						'title',
						'description',
					);
					foreach($to_register as $string) {
						icl_register_string($this->id, $this->id.'_'.$string, $this->settings[$string]);
					}
				}

				/**
				 * Initialise Gateway Settings Form Fields
				 * 'setting-name' => array(
				 *		'title' => __( 'Title for setting', 'woothemes' ),
				 *		'type' => 'checkbox|text|textarea',
				 *		'label' => __( 'Label for checkbox setting', 'woothemes' ),
				 *		'description' => __( 'Description for setting' ),
				 *		'default' => 'default value'
				 *	),
				 */
				function init_form_fields() {
				
					$this->form_fields = array(
						'enabled' => array(
										'title' => __('Ativar/Desativar', 'woocommerce'), 
										'type' => 'checkbox', 
										'label' => __( 'Ativar Pagamento por Multibanco e / ou Payshop (utilizando LUSOPAY)', 'woocommerce'), 
										'default' => 'yes'
									),
						'only_portugal' => array(
										'title' => __('Apenas para clientes de Portugal?', 'woocommerce'), 
										'type' => 'checkbox', 
										'label' => __( 'Ativar apenas para clientes cuja morada seja em Portugal', 'woocommerce'), 
										'default' => 'no'
									),
						'only_above' => array(
										'title' => __('Apenas para encomendas acima de', 'woocommerce'), 
										'type' => 'number', 
										'description' => __( 'Activar apenas para encomendas acima de x &euro; (exclusive). Deixe em branco (ou zero) para activar para qualquer valor de encomenda.', 'woocommerce').' <br/> '.__( 'O serviço Multibanco apenas aceita pagamentos entre 1 e 999999 &euro; (inclusivé). Pode utilizar esta opção para limitar ainda mais este intervalo de valores.', 'woocommerce'), 
										'default' => ''
									),
						'only_bellow' => array(
										'title' => __('Apenas para encomendas a baixo de', 'woocommerce'), 
										'type' => 'number', 
										'description' => __( 'Activar apenas para encomendas abaixo de x &euro; (exclusive). Deixe em branco (ou zero) para activar para qualquer valor de encomenda.', 'woocommerce').' <br/> '.__( 'O serviço Multibanco apenas aceita pagamentos entre 1 e 999999 &euro; (inclusivé). Pode utilizar esta opção para limitar ainda mais este intervalo de valores.', 'woocommerce'), 
										'default' => ''
									),
						'stock_when' => array(
										'title' => __('Reduzir stock', 'woocommerce'), 
										'type' => 'select', 
										'description' => __( 'Escolha quando reduzir o stock..', 'woocommerce'), 
										'default' => '',
										'options'	=> array(
											''		=> __('quando a encomenda é paga (requer callback ativo)', 'woocommerce'),
											'order'	=> __('quando a encomenda é colocada (antes do pagamento)', 'woocommerce'),
										),
									),
						'title' => array(
										'title' => __('Título', 'woocommerce' ), 
										'type' => 'text', 
										'description' => __('Isto controla o título que o utilizador vê durante o checkout.', 'woocommerce'), 
										'default' => __('Multibanco e / ou Payshop (by LUSOPAY)', 'woocommerce')
									),
						'description' => array(
										'title' => __('Descrição', 'woocommerce' ), 
										'type' => 'textarea',
										'description' => __('Isto controla a descrição que o utilizador vê durante o checkout.', 'woocommerce' ), 
										'default' => __('Pagamento de Serviços, com entidade e referência, em qualquer caixa Multibanco ou através do seu serviço de homebanking para Payshop pode pagar num agente respectivo. (Apenas disponível para clientes de bancos Portugueses)', 'woocommerce')
									),
						'chave' => array(
										'title' => __('ClientGuid', 'woocommerce'), 
										'type' => 'text',
										'description' => __( 'ClientGuid fornecida pela LUSOPAY aquando o registo no site LUSOPAY e comunicação que quer activar o serviço Multibanco e / ou Payshop.', 'woocommerce'), 
										'default' => ''
									),
						'nif' => array(
										'title' => __('VatNumber', 'woocommerce'), 
										'type' => 'text', 
										'description' => __('O que vem no email que recebe quando ativa o serviço.', 'woocommerce'), 
										'default' => ''   
									),
						'secret_key' => array(
										'title' => __('Chave Anti-phishing', 'woocommerce'), 
										'type' => 'hidden', 
										'description' => '<b id="lusopaygateway_secret_key_label">'.$this->get_option('secret_key').'</b><br/>'.__('Para garantir a segurança do <i>callback</i>, gerada pelo sistema e que tem de ser fornecida à LUSOPAY no pedido de activação do <i>callback</i>.', 'woocommerce'), 
										'default' => $this->secret_key 
									),/*
						'debug' => array(
										'title' => __( 'Debug Log', 'woocommerce' ),
										'type' => 'checkbox',
										'label' => __( 'Enable logging', 'woocommerce' ),
										'default' => 'no',
										'description' => sprintf( __( 'Log plugin events, such as callback requests, inside <code>%s</code>', 'lusopaygateway' ), wc_get_log_file_path($this->id) ),
									),
						'debug_email' => array(
										'title' => __( 'Debug to email', 'lusopaygateway' ),
										'type' => 'email',
										'label' => __( 'Enable email logging', 'lusopaygateway' ),
										'default' => '',
										'description' => __( 'Send plugin events to this email address, such as callback requests.', 'lusopaygateway' ),
									)*/
						);
				
				}
				public function admin_options() {
					global $woocommerce;
					?>
					<h3><?php echo $this -> method_title; ?> <span style="font-size: 75%;">v.<?php echo $this -> version; ?></span></h3>
					<p><b><?php _e('Siga as instruções para ativar o serviço Multibanco e / ou Payshop e o callback:', 'woocommerce'); ?></b></p>
					<ul class="lusopaygateway_list">
						<li><?php printf(__('Registe-se através do nosso site. ' . '<a href="https://www.lusopay.com" target="_blank">https://www.lusopay.com/</a>', 'woocommerce')); ?></li>
						<li><?php printf(__('Para ativar o "Callback" na sua conta deve enviar um email para geral@lusopay.com com o assunto "Callback", colocar o NIF e copiar o URL que lhe aparece à frente a negrito:', 'woocommerce')); ?> 
							<b><?php echo $this -> notify_url; ?></b>
						</li>
					</ul>
					<hr/>
					<script type="text/javascript">
					jQuery(document).ready(function(){
						if (jQuery('#woocommerce_lusopaygateway_secret_key').val()=='') {
							jQuery('#woocommerce_lusopaygateway_secret_key').val('<?php echo $this -> secret_key; ?>');
							jQuery('#woocommerce_lusopaygateway_secret_key_label').html('<?php echo $this -> secret_key; ?>');
							jQuery('#mainform').submit();
	}
	});
</script>
<table class="form-table">
<?php
if (trim(get_woocommerce_currency())=='EUR') {
$this->generate_settings_html();
} else {
?>
<p><b><?php _e('Erro!', 'woocommerce'); ?> <?php printf(__('Selecione a moeda <b>Euros (&euro;)</b> %1$s', 'woocommerce'), '<a href="admin.php?page=woocommerce_settings&tab=general">' . __('Aqui', 'woocommerce') . '</a>.'); ?></b></p>
<?php
}
?>
</table>
<style type="text/css">
	.lusopaygateway_list {
		list-style-type: disc;
		list-style-position: inside;
	}
	.lusopaygateway_list li {
		margin-left: 1.5em;
	}
</style>
<?php
}

/**
* Icon HTML
*/
public function get_icon() {
$icon_html = '<img src="'.esc_attr($this->icon).'" alt="'.esc_attr($this->title).'" />';
return apply_filters('woocommerce_gateway_icon', $icon_html, $this->id);
}

/**
* Thank you page
*/
function thankyou($order_id) {
//$order = wc_get_order( $order_id );
$res =$this->getRef($order_id);
echo $res;
}


/**
* Email instructions
*/
function email_instructions($order, $sent_to_admin) {
global $wpdb;
if ( $order->payment_method !== $this->id) return;
switch ($order->status) {
case 'on-hold':
case 'pending':

$table_name = $wpdb->prefix . "magnimeiosreferences";

$result = $wpdb->get_results( 'SELECT * FROM '.$table_name.' WHERE id_order = '.$order->id);
$rows = count($result);

if ($rows == 1) {
echo $this->getRef($order->id);
} else {
$this->GenerateRef($this->chave,  $this->nif, $order->id, $order->order_total);
echo $this->getRef($order->id);
}
break;
case 'processing':
?>
<p><b><?php _e('Pagamento recebido.', 'woocommerce'); ?></b> <?php _e('Iremos agora processar a sua encomenda.', 'woocommerce'); ?></p>
<?php
break;
default:
return;
break;
}
}


/**
* Process it
*/
function process_payment($order_id) {
global $woocommerce;
$order=new WC_Order($order_id);
// Mark as on-hold
$order->update_status('on-hold', __('Aguardar Pagamento por Multibanco e / ou Payshop.', 'woothemes'));
// Reduce stock levels
if ($this->stock_when=='order')
$order->reduce_order_stock();
// Remove cart
$woocommerce->cart->empty_cart();
// Empty awaiting payment session
unset($_SESSION['order_awaiting_payment']);
// Return thankyou redirect
return array(
'result' => 'success',
'redirect' => $this->get_return_url($order)
);
}

/**
* Just for Portugal
*/
function disable_unless_portugal($available_gateways) {
global $woocommerce;
if (isset($available_gateways[$this->id])) {
if (trim($available_gateways[$this->id]->only_portugal)=='yes' && trim($woocommerce->customer->get_country())!='PT') unset($available_gateways[$this->id]);
}
return $available_gateways;
}

/**
* Just above/bellow certain amounts
*/
function disable_only_above_or_bellow($available_gateways) {
global $woocommerce;
if (isset($available_gateways[$this->id])) {
if (@floatval($available_gateways[$this->id]->only_above)>0) {
if($woocommerce->cart->total<floatval($available_gateways[$this->id]->only_above)) {
unset($available_gateways[$this->id]);
}
}
if (@floatval($available_gateways[$this->id]->only_bellow)>0) {
if($woocommerce->cart->total>floatval($available_gateways[$this->id]->only_bellow)) {
unset($available_gateways[$this->id]);
}
}
}
return $available_gateways;
}



				function callback() {
					
					
					
					@ob_clean();
					//We must 1st check the situation and then process it and send email to the store owner in case of error.
					if (isset($_GET['chave']) && isset($_GET['entidade']) && isset($_GET['referencia'])	&& isset($_GET['valor'])) {
						//Let's process it
						//if ($this->debug) $this->log->add($this->id, '- Callback ('.$_SERVER['REQUEST_URI'].') with all arguments from '.$_SERVER['REMOTE_ADDR']);
						$ref=trim(str_replace(' ', '', $_GET['referencia']));
						$ent=trim($_GET['entidade']);
						$valor=$_GET['valor'];
						$val=str_replace(',','.', $valor);
						$chave = trim($_GET['chave']);
						
						//wp_die($_SERVER['HTTP_HOST'].' '.$_SERVER['REQUEST_URI']);
						
						if ($chave==trim($this->secret_key) && $val>=1) {
							if($ent=='11024'){
								$order_id = $this->getLusopayReferencesMBOrderIdDb($ref, $val);
								
							}
							else if($ent=='10120'){
								$refPS= $ent.$ref;
								$order_id = $this->getLusopayReferencesPSOrderIdDb($refPS, $val);
							}
								
							if($order_id!=0){
								
								$order = new WC_Order($order_id);
								
								include_once(ABSPATH.'wp-admin/includes/plugin.php' );
								if (!is_plugin_active('order-status-emails-for-woocommerce/order-status-emails-for-woocommerce.php')) //Only if this plugin is not active
								if ($order->status!='pending') $order->update_status('pending', __('Temporary status. Used to force an email on the next order status change.', 'woocommerce'));
								if ($this->stock_when=='') $order->reduce_order_stock();
								$order->update_status('processing', __('Pagamento recebido.', 'woocommerce')); //Paid
								$this->updateStatus($order_id);
								
								
								header('Location: https://www.lusopay.com/callback/callback_response_true.php');
								
							}else{
								
								//wp_die('entrou');

								$response = http_response_code();//ttpRequest->getResponseBody();
								if($response==200){
									
									header('Location: https://www.lusopay.com/callback/callback_response_false.php');
									
								}
								
								 
							}
								
						}
						
						 else {
							header('Location: https://www.lusopay.com/callback/callback_response_false.php');
						}
					} else {
						header('Location: https://www.lusopay.com/callback/callback_response_false.php');
					}
				}



function getLusopayReferencesMBOrderIdDb ($ref, $valor, $order_id = 0){


global $wpdb;

$table_name = $wpdb->prefix . "magnimeiosreferences";



$result = $wpdb->get_results("SELECT * FROM ".$table_name." WHERE refMB = ".$ref." AND value=".$valor." AND status is null");


foreach ($result as $row) {
$order_id = $row->id_order;
$refs[2] = $row->refMB;
$refs[1] = $row->refPS;
$order_value = $row->value;

}

return $order_id;

}

function getLusopayReferencesPSOrderIdDb ($ref, $valor, $order_id=0){

global $wpdb;

$table_name = $wpdb->prefix . "magnimeiosreferences";

$refPS = "%".$ref."%";

$result = $wpdb->get_results( "SELECT * FROM ".$table_name." WHERE refPS LIKE \"".$refPS."\" AND value=".$valor." AND status is null");

foreach ($result as $row) {
$order_id = $row->id_order;
$refs[2] = $row->refMB;
$refs[1] = $row->refPS;
$order_value = $row->value;

}

return $order_id;

}



function updateStatus($order_id){
	
	global $wpdb;
	
	$set = 'PAGO';
	
	$table_name = $wpdb->prefix . "magnimeiosreferences";
	
	
	$wpdb->update($table_name, array('status' => $set), array('id_order' => $order_id));
	 
	
}




function format_number($number)
{
$verifySepDecimal = number_format(99,2);

$valorTmp = $number;

$sepDecimal = substr($verifySepDecimal, 2, 1);

$hasSepDecimal = True;

$i=(strlen($valorTmp)-1);

for($i;$i!=0;$i-=1)
{
if(substr($valorTmp,$i,1)=="." || substr($valorTmp,$i,1)==","){
$hasSepDecimal = True;
$valorTmp = trim(substr($valorTmp,0,$i))."@".trim(substr($valorTmp,1+$i));
break;
}
}

if($hasSepDecimal!=True){
$valorTmp=number_format($valorTmp,2);

$i=(strlen($valorTmp)-1);

for($i;$i!=1;$i--)
{
if(substr($valorTmp,$i,1)=="." || substr($valorTmp,$i,1)==","){
$hasSepDecimal = True;
$valorTmp = trim(substr($valorTmp,0,$i))."@".trim(substr($valorTmp,1+$i));
break;
}
}
}

for($i=1;$i!=(strlen($valorTmp)-1);$i++)
{
if(substr($valorTmp,$i,1)=="." || substr($valorTmp,$i,1)=="," || substr($valorTmp,$i,1)==" "){
$valorTmp = trim(substr($valorTmp,0,$i)).trim(substr($valorTmp,1+$i));
break;
}
}

if (strlen(strstr($valorTmp,'@'))>0){
$valorTmp = trim(substr($valorTmp,0,strpos($valorTmp,'@'))).trim($sepDecimal).trim(substr($valorTmp,strpos($valorTmp,'@')+1));
}

return $valorTmp;
}
//FIM TRATAMENTO DEFINI��ES REGIONAIS

//INICIO REF MULTIBANCO

function GenerateRef($ent_chave, $ent_nif, $order_id, $order_value)
{

//$order_id ="0000".$order_id;

//$order_id ="0000"."123456";

$order_value= sprintf("%01.2f", $order_value);

$order_value =  $this->format_number($order_value);

//Apenas sao considerados os 4 caracteres mais a direita do order_id
//$order_id = substr($order_id, (strlen($order_id) - 4), strlen($order_id));

$soapUrl = "https://services.lusopay.com/PaymentServices/PaymentServices.svc?wsdl";

$xml_post_string='<?xml version="1.0" encoding="utf-8"
?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/" xmlns:pay="http://schemas.datacontract.org/2004/07/PaymentServices">
<soapenv:Body>
<tem:getNewDynamicReference>
<!--Optional:-->
<tem:clientGuid>'.$ent_chave.'</tem:clientGuid>
<!--Optional:-->
<tem:vatNumber>'.$ent_nif.'</tem:vatNumber>
<!--Optional:-->
<tem:valueList>
<!--Zero or more repetitions:-->
<pay:References>
<!--Optional:-->
<pay:amount>'.$order_value.'</pay:amount>
<!--Optional:-->
<pay:description>'.$order_id.'</pay:description>
<!--Optional:-->
<pay:serviceType>Both</pay:serviceType>
</pay:References>
</tem:valueList>
<!--Optional:-->
<tem:sendEmail>true</tem:sendEmail>
</tem:getNewDynamicReference>
</soapenv:Body>
</soapenv:Envelope>';

$headers = array(
"Host: services.lusopay.com",
"Content-type: text/xml;charset=\"utf-8\"",
"Accept: text/xml",
"Cache-Control: no-cache",
"Pragma: no-cache",
"SOAPAction: http://tempuri.org/IPaymentServices/getNewDynamicReference",
"Content-length: ".strlen($xml_post_string),
);

//SOAPAction: your op URL
$url = $soapUrl;
// PHP cURL for http connection with auth
$ch = curl_init();

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_URL, $url);

curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);

curl_close($ch);

$referenceMB = "/<a:referenceMB>(.*?)<\/a:referenceMB>/s";
$referencePS = "/<a:referencePS>(.*?)<\/a:referencePS>/s";
if(preg_match($referencePS,$response,$referencePS_value) && preg_match($referenceMB, $response, $referenceMB_value)) {
$refs[1] = $referencePS_value[1];
$refs[2] = $referenceMB_value[1];

global $wpdb;

$table_name = $wpdb->prefix . "magnimeiosreferences";

$wpdb->insert( $table_name, array( 'id_order' => $order_id, 'refMB' => $refs[2], 'refPS' => $refs[1], 'value' => $order_value));

}
else {
$message = "/<a:message>(.*?)<\/a:message>/s";
if(preg_match($message,$response,$message_value)) {
echo $message_value[1];
}
}

return true;

}

function getRef($order_id){
global $wpdb;

$table_name = $wpdb->prefix . "magnimeiosreferences";

$result = $wpdb->get_results( "SELECT * FROM ".$table_name." WHERE id_order = ".$order_id);

foreach ($result as $row) {
$refs[2] = $row->refMB;
$refs[1] = $row->refPS;
$order_value = $row->value;

if($refs[2] != -1){

$refs[2] = substr($refs[2],0,3).' '.substr($refs[2],3,3).' '.substr($refs[2],6,3);

$tabelaMulti = '<table cellpadding="3" width="400px" cellspacing="0" style="margin-top: 10px;border: 1px solid #dcdcdc" align="center">';
$tabelaMulti .= "	<tr>";
$tabelaMulti .=	'		<td style="font-size: x-small; border-top: 0px; border-left: 0px; border-right: 0px; border-bottom: 1px solid #dcdcdc; background-color: #dcdcdc; color: black" colspan="3"><center>Pagamento por Multibanco (by LUSOPAY)</center></td>';
$tabelaMulti .= "	</tr>";
$tabelaMulti .= "	<tr>";
$tabelaMulti .=	'		<td rowspan="4"><div align="center"><img src="https://www.lusopay.com/App_Files/cms/documents/images/Logo_Lusopay_MB125x80px.png" alt=""/></div></td>';
$tabelaMulti .=	'		<td style="font-size: x-small; font-weight:bold; text-align:left">Entidade:</td>';
$tabelaMulti .=	'		<td style="font-size: x-small; text-align:left">11024</td>';
$tabelaMulti .= "	</tr>";
$tabelaMulti .= "	<tr>";
$tabelaMulti .= '	<td style="font-size: x-small; font-weight:bold; text-align:left">Refer&ecirc;ncia:</td>';
$tabelaMulti .=	'		<td style="font-size: x-small; text-align:left">'. $refs[2].'</td>';
$tabelaMulti .= "	</tr>";
$tabelaMulti .= "	<tr>";
$tabelaMulti .=	'		<td style="font-size: x-small; font-weight:bold; text-align:left">Valor:</td>';
$tabelaMulti .=	'		<td style="font-size: x-small; text-align:left">'.number_format($order_value, 2,',', ' ').'&nbsp;&euro;</td>';
$tabelaMulti .= "	</tr>";
$tabelaMulti .= "	<tr>";
$tabelaMulti .= '		<td style="font-size: x-small; font-weight:bold; text-align:left">&nbsp;</td>';
$tabelaMulti .= '		<td style="font-size: x-small; text-align:left">&nbsp;</td>';
$tabelaMulti .= "	</tr>";
$tabelaMulti .= "	<tr>";
$tabelaMulti .=	'		<td style="font-size: xx-small;border-top: 1px solid #dcdcdc; border-left: 0px; border-right: 0px; border-bottom: 0px; background-color: #dcdcdc; color: black" colspan="3"><center>O tal&atilde;o emitido pela caixa autom&aacute;tica faz prova de pagamento. Conserve-o.</center></td>';
$tabelaMulti .= "	</tr>";
$tabelaMulti .= "</table>";
}

if($refs[1] != -1){

$refs[1] = substr($refs[1],0,3).' '.substr($refs[1],3,3).' '.substr($refs[1],6,3).' '.substr($refs[1],9,3).' '.substr($refs[1],12,1);

$tabelaMulti .= '<table cellpadding="3" width="400px" cellspacing="0" style="margin-top: 10px;border: 1px solid #dcdcdc" align="center">';
$tabelaMulti .= 	"<tr>";
$tabelaMulti .=	'		<td style="font-size: x-small; border-top: 0px; border-left: 0px; border-right: 0px; border-bottom: 1px solid #dcdcdc; background-color: #dcdcdc; color: black" colspan="3"><center>Pagamento por Payshop (by LUSOPAY)</center></td>';
$tabelaMulti .= "	</tr>";
$tabelaMulti .= "	<tr>";
$tabelaMulti .=	'		<td rowspan="4"><div align="center"><img src="https://www.lusopay.com/App_Files/cms/documents/images/Logo_Lusopay_Payshop125x80px.png" alt=""/></div></td>';
$tabelaMulti .= "	</tr>";
$tabelaMulti .= "	<tr>";
$tabelaMulti .=	'		<td style="font-size: x-small; font-weight:bold; text-align:left">Refer&ecirc;ncia:</td>';
$tabelaMulti .=	'		<td style="font-size: x-small; text-align:left">'. $refs[1].'</td>';
$tabelaMulti .= "	</tr>";
$tabelaMulti .= "	<tr>";
$tabelaMulti .=	'		<td style="font-size: x-small; font-weight:bold; text-align:left">Valor:</td>';
$tabelaMulti .=	'		<td style="font-size: x-small; text-align:left">'.number_format($order_value, 2,',', ' ').'&nbsp;&euro;</td>';
$tabelaMulti .= "	</tr>";
$tabelaMulti .= "	<tr>";
$tabelaMulti .= '		<td style="font-size: x-small; font-weight:bold; text-align:left">&nbsp;</td>';
$tabelaMulti .= '		<td style="font-size: x-small; text-align:left">&nbsp;</td>';
$tabelaMulti .= "	</tr>";
$tabelaMulti .= "	<tr>";
$tabelaMulti .=	'		<td style="font-size: xx-small;border-top: 1px solid #dcdcdc; border-left: 0px; border-right: 0px; border-bottom: 0px; background-color: #dcdcdc; color: black" colspan="3"><center>O tal&atilde;o emitido faz prova de pagamento. Conserve-o.</center></td>';
$tabelaMulti .= "	</tr>";
$tabelaMulti .= '</table>';
}

}
return $tabelaMulti;

//return true;
}




}

}




}

function add_lusopaygateway_gateway( $methods ) {
$methods[] = 'WC_Lusopaygateway';
return $methods;
}
add_filter( 'woocommerce_payment_gateways', 'add_lusopaygateway_gateway' );



}
