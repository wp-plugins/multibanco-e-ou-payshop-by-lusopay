<?php
/*
Plugin Name: WooCommerce LusopayGateway
Plugin URI: https://www.lusopay.com
Description: Plugin oficial da LUSOPAY para WooCommerce para Pagamentos por Multibanco e / ou Payshop. Para o poder utilizar tem de efetuar um contrato com a <a href="https://www.lusopay.com" target="_blank">LUSOPAY</a> para poder utilizar este m&oacute;dulo. Para mais informa&ccedil;&otilde;es de como aderir <a href="https://www.lusopay.com" target="_blank">clique aqui</a>.
Version: 1.1.0
Author: LUSOPAY
Author URI: https://www.lusopay.com
*/
add_action('plugins_loaded', 'woocommerce_lusopaygateway_init', 0);

function woocommerce_lusopaygateway_init() {
 
    if ( ! class_exists( 'WC_Payment_Gateway' ) ) { return; }
	
	class WC_Lusopaygateway extends WC_Payment_Gateway {
		public function __construct() {
			$this->id = 'lusopaygateway';
			//$this->icon = apply_filters('woocommerce_multibanco_icon', '');
			$this->icon 			= WP_PLUGIN_URL . "/" . plugin_basename( dirname(__FILE__)) . '/imagens/Logo_Lusopay_MBePayshop52x33px.png';
			$this->has_fields = false;
		 
			// Load the form fields.
			$this->init_form_fields();
		 
			// Load the settings.
			$this->init_settings();
		 
			// Define user set variables
			$this->title = $this->settings['title'];
			$this->description = $this->settings['description'];
			$this->chave = $this->settings['chave'];
			$this->nif = $this->settings['nif'];

			global $wpdb;
			global $mag_db_version;

			$table_name = $wpdb->prefix . "magnimeiosreferences";

			$sql = 'CREATE TABLE IF NOT EXISTS '.$table_name.' (id_order int, refMB VARCHAR(9), refPS VARCHAR(13), value VARCHAR(10))';

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			add_option( "mag_db_version", $mag_db_version );

			// Actions
			add_action('woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));
			
			add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ));
			
			add_action('woocommerce_thankyou_lusopaygateway', array(&$this, 'thankyou_page'));
		
			// Customer Emails
			add_action('woocommerce_email_after_order_table', array($this, 'email_instructions'));
			
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
								'title' => __( 'Enable/Disable', 'woocommerce' ), 
								'type' => 'checkbox', 
								'label' => __( 'Ativar Pagamento por Multibanco e / ou Payshop', 'woocommerce' ), 
								'default' => 'yes'
							), 
				'title' => array(
								'title' => __( 'Title', 'woocommerce' ), 
								'type' => 'text', 
								'description' => __( 'Controla o t&iacute;tulo que utilizador vai visualizar durante o checkout.', 'woocommerce' ), 
								'default' => __( 'Pagamento por Multibanco e / ou Payshop (by LUSOPAY)', 'woocommerce' )
							),
				'description' => array(
								'title' => __( 'Mensagem para o Cliente', 'woocommerce' ), 
								'type' => 'textarea', 
								'description' => __( 'Deixe uma mensagem ao seu cliente para ele saber que estes meios de pagamento s&atilde;o mais comodo e seguro para ele.', 'woocommerce' ), 
								'default' => __( 'Maior facilidade e simplicidade de pagamento podendo o mesmo ser efetuado em qualquer terminal Multibanco ou Homebanking e no caso do Payshop nos respetivos agentes.', 'woocommerce' )    
							),
				'chave' => array(
								'title' => __( 'Chave', 'woocommerce' ), 
								'type' => 'text', 
								'description' => __( 'Chave fornecida pela LUSOPAY no ato do contrato.', 'woocommerce' ), 
								'default' => __( '', 'woocommerce' )    
							),
				'nif' => array(
								'title' => __( 'NIF', 'woocommerce' ), 
								'type' => 'text', 
								'description' => __( 'O NIF que colocou no contrato.', 'woocommerce' ), 
								'default' => __( '', 'woocommerce' )    
							)
				);
		
		} // End init_form_fields()
		
		public function admin_options() {
			?>
			<img src="<?php echo WP_PLUGIN_URL . "/" . plugin_basename( dirname(__FILE__)) . '/imagens/Logo_Lusopay_256x85px.png' ?>" />
			<h3><?php _e('Pagamento por Multibanco e / ou Payshop', 'woothemes'); ?></h3>
			<p><?php _e('Permite a emiss&atilde;o de Refer&ecirc;ncias Multibanco e / ou Payshop na sua loja online, que podem ser pagas na rede Multibanco ou Homebanking, e no caso Payshop nos respetivos agentes.', 'woothemes'); ?></p>
			<table class="form-table">
			<?php
				// Generate the HTML For the settings form.
				$this->generate_settings_html();
			?>
			</table>
			<?php
			} // End admin_options()

			function thankyou_page($order_id) {

			//$order = &new WC_Order( $order_id );
			$order = wc_get_order( $order_id );

			$res =$this->getRef($order_id);
			echo $res;
			
			}

			/**
			* Add text to user email
			**/
			function email_instructions($order) {
				
			//var_dump($sent_to_admin);	

			//if ( $sent_to_admin ) return;
			
			//var_dump('tetsdedfe');

			if ( $order->status !== 'on-hold') return;

			if ( $order->payment_method !== 'lusopaygateway') return;
			
			global $wpdb;

			$table_name = $wpdb->prefix . "magnimeiosreferences";

			$result = $wpdb->get_results( 'SELECT * FROM '.$table_name.' WHERE id_order = '.$order->id);
			$rows = count($result);
			
			if($rows == 1){
				echo $this->getRef($order->id);
				
				
			}
			else{
				$this->GenerateRef($this->chave,  $this->nif, $order->id, $order->order_total);
				echo $this->getRef($order->id);	
			}
				
			
			//$this->getRef($order->id);
			
			}

			function process_payment( $order_id ) {
			global $woocommerce;

			//$order = &new WC_Order( $order_id );
			
			$order = wc_get_order( $order_id );

			// Mark as on-hold (we're awaiting the cheque)
			$order->update_status('on-hold', __('Aguardar Pagamento por Multibanco e / ou Payshop', 'woothemes'));
			
			// Reduce stock levels
			$order->reduce_order_stock();
			
			// Remove cart
			$woocommerce->cart->empty_cart();
			
			//$this->GenerateRef($this->chave,  $this->nif, $order->id, $order->order_total);
			//$this->getRef($order->id);

			// Empty awaiting payment session
			//unset($_SESSION['order_awaiting_payment']);

			// Return thankyou redirect
			return array(
			'result'    => 'success',
			'redirect'	=> $this->get_return_url( $order )
			);

			}

			//INICIO TRATAMENTO DEFINI��ES REGIONAIS
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

			if ($order_value < 2){
			echo "Lamentamos mas � imposs�vel gerar uma refer�ncia MB para valores inferiores a 2 Euro";
			return;
			}
			
			
			

			$soapUrl = "https://services.lusopay.com/PaymentServices/PaymentServices.svc?wsdl";
		
		$xml_post_string='<?xml version="1.0" encoding="utf-8"?>
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

			$result = $wpdb->get_results( 'SELECT * FROM '.$table_name.' WHERE id_order = '.$order_id);
			

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


			function add_lusopaygateway_gateway( $methods ) {
			$methods[] = 'WC_Lusopaygateway'; return $methods;
			}
			add_filter('woocommerce_payment_gateways', 'add_lusopaygateway_gateway' );
			

			global $mag_db_version;
			$mag_db_version = "1.0";

			}
		?>