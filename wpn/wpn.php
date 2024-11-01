<?php
/***************************************************************
	@
	@	Web Push Notifications
	@	bassem.rabia@gmail.com
	@
/**************************************************************/
class wpn{
	public function __construct(){
		$this->Signature = array(
			'pluginName' => 'Web Push Notifications',
			'pluginNiceName' => 'Push Notif',
			'pluginSlug' => 'web-push-notifications',
			'pluginSelector' => 'WebPushNotifications',
			'pluginVersion' => '1.1',
			'installationId' => $this->installationId(),
			'remoteURL' => 'https://api.norfolky.com/push/'
		); 		
		// echo '<pre>';print_r($this->Signature);echo '</pre>'; 
		// delete_option($this->Signature['pluginSlug'], $pluginOptions);	
		
		add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue'));
		add_action('load-post.php', array(&$this, 'meta_boxes_setup'));
		add_action('load-post-new.php', array(&$this, 'meta_boxes_setup'));
		add_action('admin_menu', array(&$this, 'menu'));
		add_action('wp_head', array(&$this, 'run_api'));
	}
	
	public function installationId(){
		function s4($n){
			$str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			return substr(str_shuffle($str), 0, $n);
		};			
		return s4(10).'-'.s4(5).'-'.s4(5).'-'.s4(5).'-'.s4(15);
	}
			
	public function run_api(){
		wp_enqueue_script($this->Signature['pluginSlug'].'-script', plugins_url('js/'.$this->Signature['pluginSlug'].'.js', __FILE__)); 
		// echo 'run_api';
		$pluginOptions = get_option($this->Signature['pluginSlug']);					
		// echo '<pre>';print_r($pluginOptions);
		if($pluginOptions['pluginPrivateKey'] != '' AND $pluginOptions['pluginPublicKey'] != ''){
			wp_enqueue_style('web-push-notifications-style', plugins_url('css/web-push-notifications.css', __FILE__));
			$fields = array
			(				
				'platform' => 'WP',
				'action' => 'Subscriber',
				'pKey' => $pluginOptions['pluginPublicKey'],
			);
			// print_r($fields);			
			$url = $pluginOptions['remoteURL'].'webPush.php?'.http_build_query($fields);
			// echo $url;
			?>			
			<script>
				$(document).ready(function(){
					window._webPushApi = {
						apiName: '<?php echo $pluginOptions['pluginName'];?>',
						apiVersion: '<?php echo $pluginOptions['pluginVersion'];?>',
						pName: '<?php echo get_bloginfo('name');?>', 
						pKey: '<?php echo $pluginOptions['pluginPublicKey'];?>',
						actionMessageInterval: '<?php echo $pluginOptions['actionMessageInterval'];?>'
					};					
					(function(jQuery){
						webPushApi.init(jQuery);	
						if(webPushApi.getCookie('webPushApi-closed') === null){
							bQuery('body').append('<div class="web-push-notifications"><span>X</span><a target="_blank" href="<?php echo $url;?>"><?php echo $pluginOptions['actionMessage'];?></a></div><div class="web-push-notifications-overlay"></div>');
							
							var _left = (bQuery(window).innerWidth()/2) - (bQuery('.web-push-notifications').width()/2);
							var _top = (bQuery(window).innerHeight()/2) - (bQuery('.web-push-notifications').height()/2); 
							bQuery('.web-push-notifications').attr('style', 'position:fixed !important; left:'+_left+'px !important; top:'+_top+'px !important');
						}
					})(jQuery);
				})
			</script>
		<?php
		}
	}
	
	public function meta_boxes_setup(){
		add_meta_box(
			$this->Signature['pluginSlug'],
			 __('Notification', 'web-push-notifications'), 
			array(&$this, 'meta_box'),
			'post',
			'side',
			'high'
		);
	}
	public function meta_box($post, $box){
		$pluginOptions = get_option($this->Signature['pluginSlug']);
		$fields = array
		(		
			'pKey' => $pluginOptions['pluginPrivateKey'],
			'title' => urlencode(get_the_title()), 
			'url' => esc_url(get_permalink($post->ID)),
			'body' => urlencode(get_the_title()),
			'installationId' => $pluginOptions['installationId'],
			'icon' => wp_get_attachment_url(get_post_thumbnail_id($post->ID))
		);
		// print_r($fields);
		$url = $pluginOptions['remoteURL'].'post.php?'.http_build_query($fields);
		// echo $url;
		?>
		<div url="<?php echo $url;?>" class="SendNotification preview button"><?php _e('Send Notification', 'web-push-notifications'); ?></div>		
		<div class="notification-response"></div>
		<div class="notification-response-notice notice is-dismissible"><p></p></div>
		<?php 
	}

	public function admin_enqueue(){
		wp_enqueue_style($this->Signature['pluginSlug'].'-admin-style', plugins_url('css/'.$this->Signature['pluginSlug'].'-admin.css', __FILE__));
		wp_enqueue_script($this->Signature['pluginSlug'].'-admin-script', plugins_url('js/'.$this->Signature['pluginSlug'].'-admin.js', __FILE__)); 
	}
	
	public function menu(){		
		add_options_page( 
			$this->Signature['pluginNiceName'], 
			$this->Signature['pluginNiceName'],
			'manage_options',
			strtolower($this->Signature['pluginSlug']).'-main-menu', 
			array(&$this, 'page')
		);
		$pluginOptions = get_option($this->Signature['pluginSlug']);
		if(count($pluginOptions)==1){
			add_option($this->Signature['pluginSlug'], $this->Signature, '', 'yes');
		}
	}
	public function page(){
		?>
		<div class="wrap columns-2 WebPushNotifications_wrap">
			<div id="<?php echo $this->Signature['pluginSelector'];?>" class="icon32"></div>  
			<h2><?php echo $this->Signature['pluginName'] .' '.$this->Signature['pluginVersion']; //echo get_locale();?></h2>			
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="postbox-container-1" class="postbox-container WebPushNotifications_container">
						<div class="postbox">
							<h3><span><?php _e('User Guide', 'web-push-notifications'); ?></span></h3>
							<div class="inside"> 
								<ol>
									<li><?php _e('Install', 'web-push-notifications'); ?></li>
									<li><?php _e('Run', 'web-push-notifications'); ?></li>
									<li><?php _e('Enjoy', 'web-push-notifications'); ?></li>
									<li><?php _e('Ask for Support if you need', 'web-push-notifications'); ?> !</li>
								</ol>
							</div>
						</div>
					</div>									
								
					<div id="postbox-container-2" class="postbox-container">
						<div id="WebPushNotifications_container">
							<?php 
								$pluginOptions = get_option($this->Signature['pluginSlug']);
								// echo '<pre>';print_r($pluginOptions);echo '</pre>';
								if(isset($_POST[$this->Signature['pluginSlug'].'-private-key'])){
									$pluginOptions['pluginPrivateKey'] = $_POST[$this->Signature['pluginSlug'].'-private-key'];
									$pluginOptions['pluginPublicKey'] = $_POST[$this->Signature['pluginSlug'].'-public-key'];
									$pluginOptions['pluginTestingKey'] = $_POST[$this->Signature['pluginSlug'].'-testing-key'];
									// echo '<pre>';print_r($pluginOptions);echo '</pre>';
									update_option($this->Signature['pluginSlug'], $pluginOptions);		
									?>
									<div class="accordion-header accordion-notification accordion-notification-success">
										<i class="fa dashicons dashicons-no-alt"></i>
										<span class="dashicons dashicons-megaphone"></span>
										<?php echo $this->Signature['pluginName'];?>
										<?php echo __('has been successfully updated', 'web-push-notifications');?>.
									</div> <?php
									$pluginOptions = get_option($this->Signature['pluginSlug']);
								}
								if(isset($_POST[$this->Signature['pluginSlug'].'-actionMessage'])){
									$pluginOptions['actionMessage'] = $_POST[$this->Signature['pluginSlug'].'-actionMessage'];
									$pluginOptions['actionMessageInterval'] = $_POST[$this->Signature['pluginSlug'].'-actionMessageInterval'];
									// echo '<pre>';print_r($pluginOptions);echo '</pre>';
									update_option($this->Signature['pluginSlug'], $pluginOptions);		
									?>
									<div class="accordion-header accordion-notification accordion-notification-success">
										<i class="fa dashicons dashicons-no-alt"></i>
										<span class="dashicons dashicons-megaphone"></span>
										<?php echo $this->Signature['pluginName'];?>
										<?php echo __('has been successfully updated', 'web-push-notifications');?>.
									</div> <?php
									$pluginOptions = get_option($this->Signature['pluginSlug']);
								}								
								// echo '<pre>';print_r($pluginOptions);echo '</pre>';
							?>								
							<div class="WebPushNotifications_service_content">
								 <div class="accordion-header">
									<i class="fa dashicons dashicons-arrow-down"></i>
									<span class="dashicons dashicons-shield"></span>
									<?php echo __('Api', 'web-push-notifications');?>
								</div>		
								<div class="WebPushNotifications_service_content WebPushNotifications_service_content_active">
									<form method="POST" action="" />
										<input placeholder="<?php echo __('Please insert your Public Key', 'web-push-notifications');?>" class="WebPushNotifications_input" type="text" name="<?php echo $this->Signature['pluginSlug'];?>-public-key" value="<?php echo $pluginOptions['pluginPublicKey'];?>" /> 
										<p class="description"><?php echo __('Public key', 'web-push-notifications');?></p>
										
										<input placeholder="<?php echo __('Please insert your Private Key', 'web-push-notifications');?>" class="WebPushNotifications_input" type="text" name="<?php echo $this->Signature['pluginSlug'];?>-private-key" value="<?php echo $pluginOptions['pluginPrivateKey'];?>" /> 
										<p class="description"><?php echo __('Private key', 'web-push-notifications');?></p>
										
										<input placeholder="<?php echo __('Please insert your Testing Key', 'web-push-notifications');?>" class="WebPushNotifications_input" type="text" name="<?php echo $this->Signature['pluginSlug'];?>-testing-key" value="<?php echo $pluginOptions['pluginTestingKey'];?>" /> 
										<p class="description"><?php echo __('Testing key', 'web-push-notifications');?></p>
										<?php										
										$fields = array
										(		
											'PartnerEmail' => get_option('admin_email'),
											'PartnerName' => get_bloginfo('name'), 
											'PartnerURL' => esc_url(get_bloginfo('url')),
											'installationId' => $pluginOptions['installationId'],
											'json' => 1,
											'utm_source' => 'wordpress'
										);
										// print_r($fields);
										$url = $pluginOptions['remoteURL'].'partner.php?'.http_build_query($fields);
										// echo $url;
										?>										
										<input class="WebPushNotifications_submit" type="submit" value="<?php echo __('Save', 'web-push-notifications');?>" />
										<?php										
										$fields = array
										(		
											'PartnerEmail' => get_option('admin_email'),
											'PartnerName' => get_bloginfo('name'), 
											'PartnerURL' => esc_url(get_bloginfo('url')),
											'installationId' => $pluginOptions['installationId'],
											'platform' => 'WP',
											'action' => 'Register',
											'utm_source' => 'wordpress'
										);
										// print_r($fields);
										$url = $pluginOptions['remoteURL'].'webPush.php?'.http_build_query($fields);	
										// echo $url;
										?>										
										<a target="_blank" class="WebPushNotifications_register" href="<?php echo $url;?>"><?php echo __('Register', 'web-push-notifications');?></a>						
									</form>
								</div>
							</div>
							<?php 
							if($pluginOptions['pluginPrivateKey'] != '' AND $pluginOptions['pluginPublicKey'] != ''){
								$fields = array
								(		
									'pKey' => $pluginOptions['pluginPublicKey'],
									'tKey' => $pluginOptions['pluginTestingKey'],
									'platform' => 'WP',
									'action' => 'TestPush'
								);
								// print_r($fields);
								$url = $pluginOptions['remoteURL'].'webPush.php?'.http_build_query($fields);
								// echo $url;
								?>
								<a target="_blank" class="WebPushNotifications_register" href="<?php echo $url;?>">
								<input class="WebPushNotifications_test" type="submit" value="<?php echo __('Test', 'web-push-notifications');?>" /></a>
								<?php 
								$fields = array
								(		
									'pKey' => $pluginOptions['pluginPublicKey'],
									'tKey' => $pluginOptions['pluginTestingKey'],
									'platform' => 'WP',
									'action' => 'Waiting'
								);
								// print_r($fields);
								$url = $pluginOptions['remoteURL'].'webPush.php?'.http_build_query($fields);
								// echo $url;
								?>
								<a target="_blank" class="WebPushNotifications_register" href="<?php echo $url;?>">
								<input class="WebPushNotifications_waiting" type="submit" value="<?php echo __('Waiting', 'web-push-notifications');?>" /></a>
								<?php 
							}
							?>
							
							<div class="WebPushNotifications_service_content">
								 <div class="accordion-header">
									<i class="fa dashicons dashicons-arrow-down"></i>
									<span class="dashicons dashicons-admin-appearance"></span>
									<?php echo __('Settings', 'web-push-notifications');?>
								</div>		
								<div class="WebPushNotifications_service_content WebPushNotifications_service_content_active">
									<form method="POST" action="" />
										<input placeholder="<?php echo __('Please insert your Call to action Message', 'web-push-notifications');?>" class="WebPushNotifications_input" type="text" name="<?php echo $this->Signature['pluginSlug'];?>-actionMessage" value="<?php echo $pluginOptions['actionMessage'];?>" /> 
										<p class="description"><?php echo __('Call to action Message', 'web-push-notifications');?></p>
										<input placeholder="<?php echo __('Please insert call for action every X days', 'web-push-notifications');?>" class="WebPushNotifications_input" type="text" name="<?php echo $this->Signature['pluginSlug'];?>-actionMessageInterval" value="<?php echo $pluginOptions['actionMessageInterval'];?>" /> 
										<p class="description"><?php echo __('Call for action every X days', 'web-push-notifications');?></p>
										
										<input class="WebPushNotifications_submit" type="submit" value="<?php echo __('Save', 'web-push-notifications');?>" />						
									</form>
								</div>
							</div>
							<?php 
							if($pluginOptions['pluginPrivateKey'] != '' AND $pluginOptions['pluginPublicKey'] != ''){
								$fields = array
								(
									'tKey' => $pluginOptions['pluginTestingKey']
								);
								// print_r($fields);
								$url = esc_url(get_bloginfo('url')).'?'.http_build_query($fields);
								// echo $url;
								?>
								<a target="_blank" class="WebPushNotifications_register" href="<?php echo $url;?>">
								<input class="WebPushNotifications_test" type="submit" value="<?php echo __('Test', 'web-push-notifications');?>" /></a>
								<p class="description"><?php echo __('Testing instructions', 'web-push-notifications');?></p>
								<?php
							}
							?>								
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php 
	}
}	 
?>