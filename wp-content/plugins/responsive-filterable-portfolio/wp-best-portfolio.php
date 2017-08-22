<?php
/*
 * Plugin Name: Responsive Filterable Portfolio
 * Plugin URI:http://www.i13websolution.com/wordpress-responsive-media-portfolio-grid.html 
 * Author URI:http://www.i13websolution.com/wordpress-responsive-media-portfolio-grid.html
 * Description:This is beautiful responsive portfolio grid with responsive lightbox.Add any number of images,links,video from admin panel. 
 * Author:I Thirteen Web Solution 
 * Version:1.0.3
 * Text Domain:responsive-filterable-portfolio
 * Domain Path: /languages
 */

add_filter ( 'widget_text', 'do_shortcode' );
add_action ( 'admin_menu', 'rfp_responsive_portfolio_plus_lightbox_add_admin_menu' );

register_activation_hook ( __FILE__, 'rfp_install_responsive_portfolio_plus_lightbox' );
add_action ( 'wp_enqueue_scripts', 'rfp_responsive_portfolio_plus_lightbox_load_styles_and_js' );
add_shortcode ( 'print_responsive_portfolio_plus_lightbox', 'rfp_print_responsive_portfolio_plus_lightbox_func' );
add_action ( 'admin_notices', 'rfp_responsive_portfolio_plus_lightbox_admin_notices' );

add_action( 'wp_ajax_rfp_check_file_exist_portfolio', 'rfp_check_file_exist_portfolio_callback' );
add_action( 'wp_ajax_rfp_get_youtube_info_portfolio', 'rfp_get_youtube_info_portfolio_callback' );
add_action( 'wp_ajax_rfp_get_metacafe_info_portfolio', 'rfp_get_metacafe_info_portfolio_callback' );

add_action('plugins_loaded', 'load_lang_for_responsive_filterable_portfolio');

function load_lang_for_responsive_filterable_portfolio() {
            
            load_plugin_textdomain( 'responsive-filterable-portfolio', false, basename( dirname( __FILE__ ) ) . '/languages/' );
 }

function rfp_save_image_curl($url,$saveto){
    $ch = curl_init ($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $raw=curl_exec($ch);
    curl_close ($ch);
    if(file_exists($saveto)){
        @unlink($saveto);
    }
    $fp = @fopen($saveto,'x');
    @fwrite($fp, $raw);
    @fclose($fp);
}

function rfp_get_youtube_info_portfolio_callback(){
  
         if(isset($_POST) and is_array($_POST) and  isset($_POST['url'])){
        

                $retrieved_nonce = '';

               if (isset($_POST['vNonce']) and $_POST['vNonce'] != '') {

                   $retrieved_nonce = $_POST['vNonce'];
               }
               if (!wp_verify_nonce($retrieved_nonce, 'vNonce')) {


                   wp_die('Security check fail');
               }

        
                $vid=htmlentities(strip_tags($_POST['vid']));
                $url=$_POST['url']; 
                $ch = curl_init();
		
		// Set the URL
		curl_setopt($ch, CURLOPT_URL, $url);
		
		// Removes the headers from the output
		curl_setopt($ch, CURLOPT_HEADER, 0);
		
		// Return the output instead of displaying it directly
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		
		// Execute the curl session
		$output = curl_exec($ch);
		
		// Close the curl session
		curl_close($ch);
                $output=json_decode($output);
                
                
              
                $return=array();
                if(is_object($output)){
                   
                 $return['title']=$output->title;
                 $return['thumbnail_url']=$output->thumbnail_url;
                
                 
               }
                
          echo json_encode($return);
          exit;
        
    }
    
}
function rfp_check_file_exist_portfolio_callback() {
	
	if(isset($_POST) and is_array($_POST) and  isset($_POST['url'])){

                
               $retrieved_nonce = '';

                if (isset($_POST['vNonce']) and $_POST['vNonce'] != '') {

                    $retrieved_nonce = $_POST['vNonce'];
                }
                    if (!wp_verify_nonce($retrieved_nonce, 'vNonce')) {


                    wp_die('Security check fail');
                }
                
		$handle = curl_init($_POST['url']);
		curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
		
		$response = curl_exec($handle);
		
		$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		
		curl_close($handle);
		
		echo trim((string)$httpCode);die;
		
	}
	//echo die;
	
}

function i13_get_http_response_code_portfolio($url) {
    $headers = @get_headers($url);
    return @substr($headers[0], 9, 3);
}
function rfp_get_metacafe_info_portfolio_callback() {
	
	  if (isset($_POST) and is_array($_POST) and isset($_POST['url'])) {

        $retrieved_nonce = '';

        if (isset($_POST['vNonce']) and $_POST['vNonce'] != '') {

            $retrieved_nonce = $_POST['vNonce'];
        }
        if (!wp_verify_nonce($retrieved_nonce, 'vNonce')) {


            wp_die('Security check fail');
        }
        
        $videoUrl = trim($_POST['url']);
        $pattern = "#(?<=watch/).*?(?=/)#";
        preg_match($pattern, $videoUrl, $matches, PREG_OFFSET_CAPTURE, 3);
        $vid = 0;
        if ($matches and is_array($matches)) {

            $vid = $matches[0][0];
        }

        $pattern_title = "#(?<=$vid/).*?(?=/)#";
        preg_match($pattern_title, $videoUrl, $matches_title, PREG_OFFSET_CAPTURE, 3);

        $title_img = '';
        if ($matches_title and is_array($matches_title)) {

            $title_img = $matches_title[0][0];
        }

        
        $trimed_vid=substr($vid, 0, -3);
        $trimed_vid=$trimed_vid.'000';
        //$imgUrl = "http://cdn.metacafe.com/thumb/{$vid}/0/6/videos/0/6/{$title_img}.jpg";
        $imgUrl = "http://cdn.metacafe.com/contents/videos_screenshots/{$trimed_vid}/{$vid}/400x225/3.jpg";
        
        if(i13_get_http_response_code_portfolio($imgUrl)!=200){
             $imgUrl = "http://cdn.metacafe.com/contents/videos_screenshots/{$trimed_vid}/{$vid}/400x225/1.jpg";
             if(i13_get_http_response_code_portfolio($imgUrl)!=200){
                 
                 $imgUrl = "http://cdn.metacafe.com/contents/videos_screenshots/{$trimed_vid}/{$vid}/180x135/3.jpg";
                 if(i13_get_http_response_code_portfolio($imgUrl)!=200){
                     
                      $imgUrl = "http://cdn.metacafe.com/contents/videos_screenshots/{$trimed_vid}/{$vid}/180x135/1.jpg";
                 }
                 
             }
            
        }
        
        
        $videoInfo = file_get_contents($videoUrl);
        $doc = new DomDocument;

        $doc->validateOnParse = false;

        $doc->loadHTML($videoInfo);
        
        $xpath = new DOMXPath($doc);
        $title='';
        $classname="mc-video-remark";
        $nodes = @$xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]/h1");
       // $nodes = $xpath->query("//div[@class='$classname']/h1");
         if(sizeof($nodes)>0){
                foreach($nodes as $nd){
                    $title= $nd->textContent;
                    break;
                }
         }
         
        $description='';
        $classname="mc-cl mc-video-description-full";
         //$nodes = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
        // $nodes = $xpath->query("*[class~=\"$classname\"]");
        
        $nodes= $xpath->query("//*[@class='" . $classname . "']/pre");
       
         
        if(sizeof($nodes)>0){
             foreach($nodes as $nd){
                    $description= $nd->textContent;
                    break;
                }
                
           
        }
       // $description = strip_tags($description);
        $description = preg_replace('/(?<=Ranked)\b.*/is', '', $description);
        $description = str_replace('Ranked', '', $description);

        $description = str_replace('quot;', '', $description);
        $description = str_replace('&amp;', '', $description);

        

      

        $returnArray = array('vid' => $vid, 'HdnMediaSelection' => $imgUrl, "videotitle" => $title, 'videotitleurl' => $videoUrl, "video_description" => $description);
        $returnArray = json_encode($returnArray);
        echo $returnArray;
        die;
    }
    echo die;
	
}

function rfp_responsive_portfolio_plus_lightbox_admin_notices() {
	if (is_plugin_active ( 'responsive-filterable-portfolio/wp-best-portfolio.php' )) {
		
		$uploads = wp_upload_dir ();
		$baseDir = $uploads ['basedir'];
		$baseDir = str_replace ( "\\", "/", $baseDir );
		$pathToImagesFolder = $baseDir . '/wp-best-portfolio';
		
		if (file_exists ( $pathToImagesFolder ) and is_dir ( $pathToImagesFolder )) {
			
			if (! is_writable ( $pathToImagesFolder )) {
				echo "<div class='updated'><p>Responsive portfolio with lightbox is active but does not have write permission on</p><p><b>" . $pathToImagesFolder . "</b> directory.Please allow write permission.</p></div> ";
			}
		} else {
			
			wp_mkdir_p ( $pathToImagesFolder );
			if (! file_exists ( $pathToImagesFolder ) and ! is_dir ( $pathToImagesFolder )) {
				echo "<div class='updated'><p>Responsive portfolio with lightbox is active but plugin does not have permission to create directory</p><p><b>" . $pathToImagesFolder . "</b> .Please create wp-best-portfolio directory inside upload directory and allow write permission.</p></div> ";
			}
		}
	}
}
function rfp_responsive_portfolio_plus_lightbox_load_styles_and_js() {
	if (! is_admin ()) {
		
		wp_enqueue_style ( 'filterMediank', plugins_url ( '/css/filterMediank.css', __FILE__ ) );
		wp_enqueue_style ( 'filterMediank-lbox', plugins_url ( '/css/filterMediank-lbox.css', __FILE__ ) );
		wp_enqueue_script ( 'jquery' );
		wp_enqueue_script ( 'filterMediank', plugins_url ( '/js/filterMediank.js', __FILE__ ) );
		wp_enqueue_script ( 'filterMediank-lbox-js', plugins_url ( '/js/filterMediank-lbox-js.js', __FILE__ ) );
		
           }
}
function rfp_install_responsive_portfolio_plus_lightbox() {
	global $wpdb;
	$table_name = $wpdb->prefix . "e_portfolio";
	
	
        $charset_collate = $wpdb->get_charset_collate();
        
	$sql = "CREATE TABLE " . $table_name . " (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `media_type` varchar(10) NOT NULL,
        `image_name` varchar(500) NOT NULL,
        `title` varchar(500) NOT NULL,
        `murl` varchar(2000) DEFAULT NULL,
        `mtag` varchar(5000) DEFAULT NULL,
        `open_link_in` tinyint(1) NOT NULL DEFAULT '0',
        `vtype` varchar(50) DEFAULT NULL,
        `vid` varchar(300) DEFAULT NULL,
        `videourl` varchar(1000) DEFAULT NULL,
        `embed_url` varchar(300) DEFAULT NULL,
        `HdnMediaSelection` varchar(300) NOT NULL,
        `createdon` datetime NOT NULL, 
         PRIMARY KEY (`id`)
        ) $charset_collate;

        ";
        
	require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta ( $sql );
	
          $best_portfolio_grid_settings=array(
                                                   
                                                    'BackgroundColor'=>'#FFFFFF',
                                                    'imagewidth'=>'273',
                                                    'imageheight'=>'160',
                                                    'imageMargin'=>'15',
                                                    'show_filters'=>'1',
                                                    'AllKeywordTranslate'=>'All',
                                                    
                                                );
               
            if( !get_option( 'best_portfolio_grid_settings' ) ) {

                 update_option('best_portfolio_grid_settings',$best_portfolio_grid_settings);
             } 
                
                
	$uploads = wp_upload_dir ();
	$baseDir = $uploads ['basedir'];
	$baseDir = str_replace ( "\\", "/", $baseDir );
	$pathToImagesFolder = $baseDir . '/wp-best-portfolio';
	wp_mkdir_p ( $pathToImagesFolder );
}
function rfp_responsive_portfolio_plus_lightbox_add_admin_menu() {
    
	$hook_suffix = add_menu_page ( __ ( 'Responsive Portfolio','responsive-filterable-portfolio' ), __ ( 'Responsive Portfolio','responsive-filterable-portfolio' ), 'administrator', 'responsive_portfolio_with_lightbox', 'rfp_responsive_portfolio_with_lightbox_admin_options_func' );
	$hook_suffix=add_submenu_page ( 'responsive_portfolio_with_lightbox', __ ( 'Portfolio Settings','responsive-filterable-portfolio' ), __ ( 'Portfolio Settings','responsive-filterable-portfolio' ), 'administrator', 'responsive_portfolio_with_lightbox', 'rfp_responsive_portfolio_with_lightbox_admin_options_func' );
	$hook_suffix_image=add_submenu_page ( 'responsive_portfolio_with_lightbox', __ ( 'Manage Media','responsive-filterable-portfolio' ), __ ( 'Manage Media','responsive-filterable-portfolio' ), 'administrator', 'responsive_portfolio_with_lightbox_media_management', 'rfp_responsive_portfolio_with_lightbox_media_management_func' );
	$hook_suffix_prev=add_submenu_page ( 'responsive_portfolio_with_lightbox', __ ( 'Preview Portfolio','responsive-filterable-portfolio' ), __ ( 'Preview Portfolio','responsive-filterable-portfolio' ), 'administrator', 'responsive_portfolio_with_lightbox_media_preview', 'rfp_responsive_portfolio_with_lightbox_media_preview_func' );
	
	add_action( 'load-' . $hook_suffix , 'rfp_responsive_portfolio_gallery_plus_lightbox_add_admin_init' );
	add_action( 'load-' . $hook_suffix_image , 'rfp_responsive_portfolio_gallery_plus_lightbox_add_admin_init' );
	add_action( 'load-' . $hook_suffix_prev , 'rfp_responsive_portfolio_gallery_plus_lightbox_add_admin_init' );
        
        rfp_responsive_portfolio_plus_lightbox_admin_scripts_init();
	
}
function rfp_responsive_portfolio_gallery_plus_lightbox_add_admin_init() {
    
        
	$url = plugin_dir_url ( __FILE__ );
	
	wp_enqueue_style ( 'filterMediank', plugins_url ( '/css/filterMediank.css', __FILE__ ) );
	wp_enqueue_style ( 'filterMediank-lbox', plugins_url ( '/css/filterMediank-lbox.css', __FILE__ ) );
	wp_enqueue_style ( 'admincss', plugins_url ( '/css/admincss.css', __FILE__ ) );
	wp_enqueue_script ( 'jquery' );
	wp_enqueue_script ( 'jquery.validate', $url . 'js/jquery.validate.js' );
	wp_enqueue_script ( 'filterMediank', plugins_url ( '/js/filterMediank.js', __FILE__ ) );
	wp_enqueue_script ( 'filterMediank-lbox-js', $url . 'js/filterMediank-lbox-js.js' );
	
	
	rfp_responsive_portfolio_plus_lightbox_admin_scripts_init();
}

function rfp_responsive_portfolio_with_lightbox_admin_options_func() {
    
	$action = 'gridview';
	if (isset ( $_GET ['action'] ) and $_GET ['action'] != '') {
		
		$action = trim ( $_GET ['action'] );
	}

		
		$url = plugin_dir_url ( __FILE__ );
		
                	if (isset ( $_POST ['btnsave'] )) {
			
                            if (!check_admin_referer('action_image_add_edit', 'add_edit_image_nonce')) {

                               wp_die('Security check fail');
                           }

                           $imageheight = (int)trim(htmlentities(strip_tags( $_POST['imageheight'] )));
                           $imagewidth = (int)trim (htmlentities(strip_tags( $_POST['imagewidth'] ),ENT_QUOTES));
                           $imageMargin = (int)trim ( htmlentities(strip_tags($_POST['imageMargin'] ),ENT_QUOTES));
                           $BackgroundColor = trim ( htmlentities(strip_tags($_POST['BackgroundColor'] ),ENT_QUOTES));
                           $AllKeywordTranslate = trim ( htmlentities(strip_tags($_POST['AllKeywordTranslate'] ),ENT_QUOTES));
                           $show_filters = htmlentities(strip_tags($_POST['show_filters']),ENT_QUOTES);

                           $best_portfolio_grid_settings=array(

                                                       'BackgroundColor'=>$BackgroundColor,
                                                       'imagewidth'=>$imagewidth,
                                                       'imageheight'=>$imageheight,
                                                       'imageMargin'=>$imageMargin,
                                                       'show_filters'=>$show_filters,
                                                       'AllKeywordTranslate'=>$AllKeywordTranslate,

                                                   );

                        
                           update_option('best_portfolio_grid_settings',$best_portfolio_grid_settings);                        

                            $wp_best_portfolio_msg=array();
                            $wp_best_portfolio_msg['type']='succ';
                            $wp_best_portfolio_msg['message']= __('Settings saved successfully.','responsive-filterable-portfolio');
                            update_option('wp_best_portfolio_msg', $wp_best_portfolio_msg);

                           $location = 'admin.php?page=responsive_portfolio_with_lightbox';
                           echo "<script type='text/javascript'> location.href='$location';</script>";
                           exit ();
		}
		
		
			
                $settings = get_option('best_portfolio_grid_settings');
	       
				
		
		?>
            <div id="poststuff" > 
                <div id="post-body" class="metabox-holder columns-2" >  
                   <div id="post-body-content">

                     <div class="wrap">
                         <table><tr><td><a href="https://twitter.com/FreeAdsPost" class="twitter-follow-button" data-show-count="false" data-size="large" data-show-screen-name="false">Follow @FreeAdsPost</a>
                                    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></td>
                                <td>
                                    <a target="_blank" title="Donate" href="http://www.i13websolution.com/donate-wordpress_image_thumbnail.php">
                                        <img id="help us for free plugin" height="30" width="90" src="<?php echo plugins_url( 'images/paypaldonate.jpg', __FILE__ ) ;?>" border="0" alt="help us for free plugin" title="help us for free plugin">
                                    </a>
                                </td>
                            </tr>
                        </table>
                          <span><h3 style="color: blue;"><a target="_blank" href="http://www.i13websolution.com/wordpress-responsive-media-portfolio-grid.html"><?php __('UPGRADE TO PRO VERSION','responsive-filterable-portfolio');?></a></h3></span>

                        <?php
                            $messages=get_option('wp_best_portfolio_msg'); 
                            $type='';
                            $message='';
                            if(isset($messages['type']) and $messages['type']!=""){

                                $type=$messages['type'];
                                $message=$messages['message'];

                            }  


                            if($type=='err'){ echo "<div class='errMsg'>"; echo $message; echo "</div>";}
                            else if($type=='succ'){ echo "<div class='succMsg'>"; echo $message; echo "</div>";}


                            update_option('wp_best_portfolio_msg', array());     
                        ?>      
                        <h2><?php echo __('Edit Portfolio',"responsive-filterable-portfolio");?></h2>
                        <br>
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<div id="post-body-content">
							<form method="post" action="" id="scrollersettiings"
								name="scrollersettiings">
								
								<div class="stuffbox" id="namediv" style="width: 100%;">
									<h3>
										<label><?php echo __('Grid Background color','responsive-filterable-portfolio');?></label>
									</h3>
									<div class="inside">
										<table>
											<tr>
												<td><input type="text" id="BackgroundColor" size="30"
													name="BackgroundColor"
													value="<?php echo $settings['BackgroundColor']; ?>"
													style="width: 100px;">
													<div style="clear: both"></div>
													<div></div></td>
											</tr>
										</table>

										<div style="clear: both"></div>
									</div>
								</div>
								<div class="stuffbox" id="namediv" style="width: 100%;">
									<h3>
										<label><?php echo __('Thumbnail Height','responsive-filterable-portfolio');?></label>
									</h3>
									<div class="inside">
										<table>
											<tr>
												<td><input type="text" id="imageheight" size="30"
													name="imageheight"
													value="<?php echo $settings['imageheight']; ?>"
													style="width: 100px;">
													<div style="clear: both"></div>
													<div></div></td>
											</tr>
										</table>

										<div style="clear: both"></div>
									</div>
								</div>
								<div class="stuffbox" id="namediv" style="width: 100%;">
									<h3>
										<label><?php echo __('Thumbnail Width','responsive-filterable-portfolio');?></label>
									</h3>
									<div class="inside">
										<table>
											<tr>
												<td><input type="text" id="imagewidth" size="30"
													name="imagewidth"
													value="<?php echo $settings['imagewidth']; ?>"
													style="width: 100px;">
													<div style="clear: both"></div>
													<div></div></td>
											</tr>
										</table>

										<div style="clear: both"></div>
									</div>
								</div>
								<div class="stuffbox" id="namediv" style="width: 100%;">
									<h3>
										<label><?php echo __('Thumbnail Margin','responsive-filterable-portfolio');?></label>
									</h3>
									<div class="inside">
										<table>
											<tr>
												<td><input type="text" id="imageMargin" size="30"
													name="imageMargin"
													value="<?php echo $settings['imageMargin']; ?>"
													style="width: 100px;">
													<div style="clear: both; padding-top: 5px"><?php echo __('Gap between two images','responsive-filterable-portfolio');?></div>
													<div></div></td>
											</tr>
										</table>

										<div style="clear: both"></div>
									</div>
								</div>
								<div class="stuffbox" id="namediv" style="width: 100%;">
									<h3>
										<label><?php echo __('All Keyword Translate','responsive-filterable-portfolio');?></label>
									</h3>
									<div class="inside">
										<table>
											<tr>
												<td><input type="text" id="AllKeywordTranslate" size="30"
													name="AllKeywordTranslate"
													value="<?php echo $settings['AllKeywordTranslate']; ?>"
													style="width: 100px;">
													<div style="clear: both; padding-top: 5px"><?php echo __('Used in filter','responsive-filterable-portfolio');?></div>
													<div></div></td>
											</tr>
										</table>

										<div style="clear: both"></div>
									</div>
								</div>
								<div class="stuffbox" id="namediv" style="width: 100%;">
									<h3>
										<label><?php echo __('Show Filter ?','responsive-filterable-portfolio');?></label>
									</h3>
									<div class="inside">
										<table>
											<tr>
												<td><input style="width: 20px;" type='radio'
													<?php
													if ($settings ['show_filters'] == true) {
														echo "checked='checked'";
													}
													?>
													name='show_filters' value='1'><?php echo __('Yes','responsive-filterable-portfolio');?> &nbsp;<input
													style="width: 20px;" type='radio' name='show_filters'
													<?php
													if ($settings ['show_filters'] == false) {
														echo "checked='checked'";
													}
													?>
													value='0'><?php echo __('No','responsive-filterable-portfolio');?>
													<div style="clear: both"></div>
													<div></div></td>
											</tr>
										</table>
										<div style="clear: both"></div>
									</div>
								</div>
                                                                        <?php wp_nonce_field('action_image_add_edit', 'add_edit_image_nonce'); ?> 
                                                                        <input type="submit"
									name="btnsave" id="btnsave"
									value="<?php echo __('Save Changes','responsive-filterable-portfolio');?>"
									class="button-primary">&nbsp;&nbsp;<input type="button"
									name="cancle" id="cancle" value="<?php echo __('Cancel','responsive-filterable-portfolio');?>" class="button-primary"
									onclick="location.href = 'admin.php?page=responsive_portfolio_with_lightbox'">

							</form>
							<script type="text/javascript">

                                                            var $n = jQuery.noConflict();
                                                            $n(document).ready(function() {

                                                    $n("#scrollersettiings").validate({
                                                    rules: {
                                                           
                                                            BackgroundColor:{
                                                            required:true,
                                                                    maxlength:7
                                                            },
                                                            imageheight:{
                                                            required:true,
                                                                    digits:true,
                                                                    maxlength:15
                                                            },
                                                            imagewidth:{
                                                            required:true,
                                                                    digits:true,
                                                                    maxlength:15
                                                            },
                                                            imageMargin:{
                                                            required:true,
                                                                    number:true,
                                                                    maxlength:15
                                                            },
                                                            AllKeywordTranslate:{
                                                                     required:true,
                                                                    maxlength:200
                                                            }
                                                            


                                                    },
                                                            errorClass: "image_error",
                                                            errorPlacement: function(error, element) {
                                                            error.appendTo(element.next().next());
                                                            }


                                                    })
                                                    
                                                      $n('#BackgroundColor').wpColorPicker();
                                                });
                                                  </script>

						</div>
					</div>
				</div>
			
               
                                <div id="postbox-container-1" class="postbox-container" > 

                                <div class="postbox"> 
                                    <h3 class="hndle"><span></span><?php echo __('Access All Themes In One Price','responsive-filterable-portfolio');?></h3> 
                                    <div class="inside">
                                        <center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank">
                                                <img border="0" src="<?php echo plugins_url( 'images/300x250.gif', __FILE__);?>" width="250" height="250">
                                            </a></center>

                                        <div style="margin:10px 5px">

                                        </div>
                                    </div></div>
                                <div class="postbox"> 
                                    <h3 class="hndle"><span></span><?php echo __('Recommended WordPress Plugin','responsive-filterable-portfolio');?></h3> 
                                    <div class="inside">
                                        <center><a href="http://shareasale.com/r.cfm?b=838943&amp;u=675922&amp;m=64312&amp;urllink=&amp;afftrack=" target="_blank">
                                                <img src="<?php echo plugins_url( 'images/wpforms-336x280v1.png', __FILE__ );?>" width="250" height="250" border="0">
                                            </a></center>
                                        <div style="margin:10px 5px">
                                        </div>
                                    </div></div>

                            </div>   
                       </div>     
                </div>       
                <div class="clear"></div>
             </div> 
        </div>    
            <?php
	
}
function rfp_responsive_portfolio_with_lightbox_media_management_func() {
	$action = 'gridview';
	global $wpdb;
	
	
	
	if (isset ( $_GET ['action'] ) and $_GET ['action'] != '') {
		
		$action = trim ( $_GET ['action'] );
	}
	?>

        <?php
	if (strtolower ( $action ) == strtolower ( 'gridview' )) {
		
		$wpcurrentdir = dirname ( __FILE__ );
		$wpcurrentdir = str_replace ( "\\", "/", $wpcurrentdir );
		
		$uploads = wp_upload_dir ();
		$baseurl = $uploads ['baseurl'];
		$baseurl .= '/wp-best-portfolio/';
                
                  if(isset($_GET['order_by'])){
        
                    $order_by=trim($_GET['order_by']); 
                 }

                 if(isset($_GET['order_pos'])){

                    $order_pos=trim($_GET['order_pos']); 
                 }

                 $search_term_='';
                 if(isset($_GET['search_term'])){

                    $search_term_='&search_term='.urlencode($_GET['search_term']);
                 }
		?> 
      <div id="poststuff" > 
         <div id="post-body" class="metabox-holder columns-2" >  
          <div id="post-body-content">    
            <div class="wrap">
		<style type="text/css">
                .pagination {
                        clear: both;
                        padding: 20px 0;
                        position: relative;
                        font-size: 11px;
                        line-height: 13px;
                }

                .pagination span, .pagination a {
                        display: block;
                        float: left;
                        margin: 2px 2px 2px 0;
                        padding: 6px 9px 5px 9px;
                        text-decoration: none;
                        width: auto;
                        color: #fff;
                        background: #555;
                }

                .pagination a:hover {
                        color: #fff;
                        background: #3279BB;
                }

                .pagination .current {
                        padding: 6px 9px 5px 9px;
                        background: #3279BB;
                        color: #fff;
                }
                </style>
		<!--[if !IE]><!-->
		<style type="text/css">
                    @media only screen and (max-width: 800px) {
                            /* Force table to not be like tables anymore */
                            #no-more-tables table, #no-more-tables thead, #no-more-tables tbody,
                                    #no-more-tables th, #no-more-tables td, #no-more-tables tr {
                                    display: block;
                            }

                            /* Hide table headers (but not display: none;, for accessibility) */
                            #no-more-tables thead tr {
                                    position: absolute;
                                    top: -9999px;
                                    left: -9999px;
                            }
                            #no-more-tables tr {
                                    border: 1px solid #ccc;
                            }
                            #no-more-tables td {
                                    /* Behave  like a "row" */
                                    border: none;
                                    border-bottom: 1px solid #eee;
                                    position: relative;
                                    padding-left: 50%;
                                    white-space: normal;
                                    text-align: left;
                            }
                            #no-more-tables td:before {
                                    /* Now like a table header */
                                    position: absolute;
                                    /* Top/left values mimic padding */
                                    top: 6px;
                                    left: 6px;
                                    width: 45%;
                                    padding-right: 10px;
                                    white-space: nowrap;
                                    text-align: left;
                                    font-weight: bold;
                            }

                            /*
                                            Label the data
                                            */
                            #no-more-tables td:before {
                                    content: attr(data-title);
                            }
                    }
                    </style>
		<!--<![endif]-->
                <table><tr><td><a href="https://twitter.com/FreeAdsPost" class="twitter-follow-button" data-show-count="false" data-size="large" data-show-screen-name="false">Follow @FreeAdsPost</a>
                                    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></td>
                                <td>
                                    <a target="_blank" title="Donate" href="http://www.i13websolution.com/donate-wordpress_image_thumbnail.php">
                                        <img id="help us for free plugin" height="30" width="90" src="<?php echo plugins_url( 'images/paypaldonate.jpg', __FILE__ ) ;?>" border="0" alt="help us for free plugin" title="help us for free plugin">
                                    </a>
                                </td>
                            </tr>
                        </table>
                <span><h3 style="color: blue;"><a target="_blank" href="http://www.i13websolution.com/wordpress-responsive-media-portfolio-grid.html"><?php echo __('UPGRADE TO PRO VERSION','responsive-filterable-portfolio');?></a></h3></span>
                <?php
		$messages = get_option ( 'wp_best_portfolio_msg' );
		$type = '';
		$message = '';
		if (isset ( $messages ['type'] ) and $messages ['type'] != "") {
			
			$type = $messages ['type'];
			$message = $messages ['message'];
		}
		
		if ($type == 'err') {
			echo "<div class='errMsg'>";
			echo $message;
			echo "</div>";
		} else if ($type == 'succ') {
			echo "<div class='succMsg'>";
			echo $message;
			echo "</div>";
		}
		
		update_option ( 'wp_best_portfolio_msg', array () );
		?>

                <div style="width: 100%;">
			<div style="float: left; width: 100%;">
				<div class="icon32 icon32-posts-post" id="icon-edit">
					<br>
				</div>
				<h2>
					<?php echo __('Media','responsive-filterable-portfolio');?><a class="button add-new-h2"
						href="admin.php?page=responsive_portfolio_with_lightbox_media_management&action=addedit"><?php echo __('Add New','responsive-filterable-portfolio');?></a>
				</h2>
				<br />

				<form method="POST"
					action="admin.php?page=responsive_portfolio_with_lightbox_media_management&action=deleteselected"
					id="posts-filter" onkeypress="return event.keyCode != 13;">
					<div class="alignleft actions">
						<select name="action_upper" id="action_upper">
							<option selected="selected" value="-1"><?php echo __('Bulk Actions','responsive-filterable-portfolio');?></option>
							<option value="delete">delete</option>
						</select> <input type="submit" value="<?php echo __('Apply','responsive-filterable-portfolio');?>"
							class="button-secondary action" id="deleteselected"
							name="deleteselected" onclick="return confirmDelete_bulk();">
					</div>
                                         <?php
                                            
                                            $sliderid="0";
                                             if(isset($_GET['sliderid']) and $_GET['sliderid']!=""){
                                              $sliderid=(int)trim($_GET['sliderid']);   
                                             }

                                             $setacrionpage='admin.php?page=responsive_portfolio_with_lightbox_media_management&sliderid='.$sliderid;

                                             if(isset($_GET['order_by']) and $_GET['order_by']!=""){
                                              $setacrionpage.='&order_by='.$_GET['order_by'];   
                                             }

                                             if(isset($_GET['order_pos']) and $_GET['order_pos']!=""){
                                              $setacrionpage.='&order_pos='.$_GET['order_pos'];   
                                             }

                                             $seval="";
                                             if(isset($_GET['search_term']) and $_GET['search_term']!=""){
                                              $seval=trim($_GET['search_term']);   
                                             }

                                         ?>
					<br class="clear">
                                                    <?php
							global $wpdb;
							$settings = get_option('best_portfolio_grid_settings');
							
							
                                                        $order_by='id';
                                                        $order_pos="asc";

                                                        if(isset($_GET['order_by'])){

                                                           $order_by=trim($_GET['order_by']); 
                                                        }

                                                        if(isset($_GET['order_pos'])){

                                                           $order_pos=trim($_GET['order_pos']); 
                                                        }
                                                         $search_term='';
                                                        if(isset($_GET['search_term'])){

                                                           $search_term= sanitize_text_field($_GET['search_term']);
                                                        }

                                                        $query = "SELECT * FROM " . $wpdb->prefix . "e_portfolio ";
                                                        if($search_term!=''){
                                                           $query.=" where id like '%$search_term%' or title like '%$search_term%' "; 
                                                        }

                                                        $order_by=sanitize_text_field($order_by);
                                                        $order_pos=sanitize_text_field($order_pos);

                                                        $query.=" order by $order_by $order_pos";
                                                        

                                                        $rows = $wpdb->get_results ( $query ,'ARRAY_A' );
                                                        $rowCount = sizeof ( $rows );
                                                        
							?>
                                            <div style="padding-top:5px;padding-bottom:5px">
                                                    <b><?php echo __( 'Search','best-testimonial-slider');?> : </b>
                                                      <input type="text" value="<?php echo $seval;?>" id="search_term" name="search_term">&nbsp;
                                                      <input type='button'  value='<?php echo __( 'Search','best-testimonial-slider');?>' name='searchusrsubmit' class='button-primary' id='searchusrsubmit' onclick="SearchredirectTO();" >&nbsp;
                                                      <input type='button'  value='<?php echo __( 'Reset Search','best-testimonial-slider');?>' name='searchreset' class='button-primary' id='searchreset' onclick="ResetSearch();" >
                                                </div>  
                                                <script type="text/javascript" >
                                                   var $n = jQuery.noConflict();   
                                                    $n('#search_term').on("keyup", function(e) {
                                                           if (e.which == 13) {

                                                               SearchredirectTO();
                                                           }
                                                      });   
                                                 function SearchredirectTO(){
                                                   var redirectto='<?php echo $setacrionpage; ?>';
                                                   var searchval=jQuery('#search_term').val();
                                                   redirectto=redirectto+'&search_term='+jQuery.trim(encodeURIComponent(searchval));  
                                                   window.location.href=redirectto;
                                                 }
                                                function ResetSearch(){

                                                     var redirectto='<?php echo $setacrionpage; ?>';
                                                     window.location.href=redirectto;
                                                     exit;
                                                }
                                                </script>  
                                             <div id="no-more-tables">
						<table cellspacing="0" id="gridTbl" class="table-bordered table-striped table-condensed cf wp-list-table widefat">
							
							<thead>
								<tr>
									<th class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>
									<?php if($order_by=="id" and $order_pos=="asc"):?>
                                                                               
                                                                            <th><a href="<?php echo $setacrionpage;?>&order_by=id&order_pos=desc<?php echo $search_term_;?>"><?php echo __('Id','best-testimonial-slider');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/desc.png', __FILE__); ?>"/></a></th>
                                                                            <?php else:?>
                                                                                <?php if($order_by=="id"):?>
                                                                            <th><a href="<?php echo $setacrionpage;?>&order_by=id&order_pos=asc<?php echo $search_term_;?>"><?php echo __('Id','best-testimonial-slider');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/asc.png', __FILE__); ?>"/></a></th>
                                                                                <?php else:?>
                                                                                    <th><a href="<?php echo $setacrionpage;?>&order_by=id&order_pos=asc<?php echo $search_term_;?>"><?php echo __('Id','best-testimonial-slider');?></a></th>
                                                                                <?php endif;?>    
                                                                            <?php endif;?>  
									<?php if($order_by=="media_type" and $order_pos=="asc"):?>
                                                                               
                                                                            <th><a href="<?php echo $setacrionpage;?>&order_by=media_type&order_pos=desc<?php echo $search_term_;?>"><?php echo __('Media Type','best-testimonial-slider');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/desc.png', __FILE__); ?>"/></a></th>
                                                                            <?php else:?>
                                                                                <?php if($order_by=="media_type"):?>
                                                                            <th><a href="<?php echo $setacrionpage;?>&order_by=media_type&order_pos=asc<?php echo $search_term_;?>"><?php echo __('Media Type','best-testimonial-slider');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/asc.png', __FILE__); ?>"/></a></th>
                                                                                <?php else:?>
                                                                                    <th><a href="<?php echo $setacrionpage;?>&order_by=media_type&order_pos=asc<?php echo $search_term_;?>"><?php echo __('Media Type','best-testimonial-slider');?></a></th>
                                                                                <?php endif;?>    
                                                                            <?php endif;?>  
									<?php if($order_by=="title" and $order_pos=="asc"):?>

                                                                             <th><a href="<?php echo $setacrionpage;?>&order_by=title&order_pos=desc<?php echo $search_term_;?>"><?php echo __('Title','best-testimonial-slider');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/desc.png', __FILE__); ?>"/></a></th>
                                                                        <?php else:?>
                                                                            <?php if($order_by=="title"):?>
                                                                        <th><a href="<?php echo $setacrionpage;?>&order_by=title&order_pos=asc<?php echo $search_term_;?>"><?php echo __('Title','best-testimonial-slider');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/asc.png', __FILE__); ?>"/></a></th>
                                                                            <?php else:?>
                                                                                <th><a href="<?php echo $setacrionpage;?>&order_by=title&order_pos=asc<?php echo $search_term_;?>"><?php echo __('Title','best-testimonial-slider');?></a></th>
                                                                            <?php endif;?>    
                                                                        <?php endif;?>  
									<th><span></span></th>
									<?php if($order_by=="createdon" and $order_pos=="asc"):?>
                                                                               
                                                                            <th><a href="<?php echo $setacrionpage;?>&order_by=createdon&order_pos=desc<?php echo $search_term_;?>"><?php echo __('Published On','best-testimonial-slider');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/desc.png', __FILE__); ?>"/></a></th>
                                                                            <?php else:?>
                                                                                <?php if($order_by=="createdon"):?>
                                                                            <th><a href="<?php echo $setacrionpage;?>&order_by=createdon&order_pos=asc<?php echo $search_term_;?>"><?php echo __('Published On','best-testimonial-slider');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/asc.png', __FILE__); ?>"/></a></th>
                                                                                <?php else:?>
                                                                                    <th><a href="<?php echo $setacrionpage;?>&order_by=createdon&order_pos=asc<?php echo $search_term_;?>"><?php echo __('Published On','best-testimonial-slider');?></a></th>
                                                                                <?php endif;?>    
                                                                            <?php endif;?>  
									<th><span><?php echo __('Edit','responsive-filterable-portfolio');?></span></th>
									<th><span><?php echo __('Delete','responsive-filterable-portfolio');?></span></th>
								</tr>
							</thead>

							<tbody id="the-list">
                                                            <?php
								if (count ( $rows ) > 0) {
									
									global $wp_rewrite;
									$rows_per_page = 15;
									
									$current = (isset($_GET ['paged'])) ? ((int) htmlentities(strip_tags($_GET ['paged']),ENT_QUOTES)) : 1;
									$pagination_args = array (
											'base' => @add_query_arg ( 'paged', '%#%' ),
											'format' => '',
											'total' => ceil ( sizeof ( $rows ) / $rows_per_page ),
											'current' => $current,
											'show_all' => false,
											'type' => 'plain' 
									);
									
									$start = ($current - 1) * $rows_per_page;
									$end = $start + $rows_per_page;
									$end = (sizeof ( $rows ) < $end) ? sizeof ( $rows ) : $end;
									$delRecNonce = wp_create_nonce('delete_image');
									for($i = $start; $i < $end; ++ $i) {
										
										$row = $rows [$i];
										
										$id = $row ['id'];
										$editlink = "admin.php?page=responsive_portfolio_with_lightbox_media_management&action=addedit&id=$id";
										$deletelink = "admin.php?page=responsive_portfolio_with_lightbox_media_management&action=delete&id=$id&nonce=$delRecNonce";
										
										$outputimgmain = $baseurl . $row ['image_name'].'?rand='.  rand(0, 5000);
										?>
                                                                        <tr valign="top">
                                                                            <td class="alignCenter check-column" data-title="Select Record"><input
                                                                                    type="checkbox" value="<?php echo $row['id'] ?>"
                                                                                    name="thumbnails[]"></td>
                                                                            <td data-title="<?php echo __('Id','responsive-filterable-portfolio');?>" class="alignCenter"><?php echo $row['id']; ?></td>
                                                                            <td data-title="Video Type" class="alignCenter"><div>
                                                                                            <strong><?php echo $row['media_type']; ?></strong>
                                                                                    </div></td>
                                                                               <td data-title="<?php echo __('Title','responsive-filterable-portfolio');?>" class="alignCenter">
                                                                               <div>
                                                                                            <strong><?php echo $row['title']; ?></strong>
                                                                                    </div></td>
                                                                            <td class="alignCenter"><img
                                                                                    src="<?php echo $outputimgmain; ?>" style="width: 100px"
                                                                                    height="100px" /></td>
                                                                            
                                                                            <td data-title="<?php echo __('Published On','responsive-filterable-portfolio');?>" class="alignCenter"><?php echo $row['createdon'] ?></td>
                                                                            <td data-title="<?php echo __('Edit','responsive-filterable-portfolio');?>" class="alignCenter"><strong><a
                                                                                            href='<?php echo $editlink; ?>' title="edit"><?php echo __('Edit','responsive-filterable-portfolio');?></a></strong></td>
                                                                            <td data-title="<?php echo __('Delete','responsive-filterable-portfolio');?>" class="alignCenter"><strong><a
                                                                                            href='<?php echo $deletelink; ?>'
                                                                                            onclick="return confirmDelete();" title="delete"><?php echo __('Delete','responsive-filterable-portfolio');?></a> </strong></td>
                                                                    </tr>
                                                                    <?php
                                                                            }
                                                                    } else {
                                                                            ?>
                                                                    <tr valign="top" class=""
                                                                            id="">
                                                                            <td colspan="9" data-title="<?php echo __('No Record','responsive-filterable-portfolio');?>" align="center"><strong><?php echo __('No portfolio Found','responsive-filterable-portfolio');?></strong></td>
                                                                    </tr>
                                                                 <?php
								}
								?>      
                                                        </tbody>
						</table>
					</div>
                                         <?php
							if (sizeof ( $rows ) > 0) {
								echo "<div class='pagination' style='padding-top:10px'>";
								echo paginate_links ( $pagination_args );
								echo "</div>";
							}
							?>
                                         <br />
					<div class="alignleft actions">
						<select name="action" id="action_bottom">
							<option selected="selected" value="-1"><?php echo __('Bulk Actions','responsive-filterable-portfolio');?></option>
							<option value="delete"><?php echo __('Delete','responsive-filterable-portfolio');?></option>
						</select> 
                                               <?php wp_nonce_field('action_settings_mass_delete', 'mass_delete_nonce'); ?>
                                                <input type="submit" value="<?php echo __('Apply','responsive-filterable-portfolio');?>"
							class="button-secondary action" id="deleteselected"
							name="deleteselected" onclick="return confirmDelete_bulk();">
					</div>

				</form>
				<script type="text/JavaScript">

                            function  confirmDelete_bulk(){
                                            var topval=document.getElementById("action_bottom").value;
                                            var bottomVal=document.getElementById("action_upper").value;

                                            if(topval=='delete' || bottomVal=='delete'){


                                                var agree=confirm("<?php echo __('Are you sure you want to delete selected media ?','responsive-filterable-portfolio');?>");
                                                if (agree)
                                                    return true ;
                                                else
                                                    return false;
                                            }
                                     }
                                     
                            function  confirmDelete(){
                             var agree=confirm("<?php echo __('Are you sure you want to delete this media ?','responsive-filterable-portfolio');?>");
                             if (agree)
                                 return true ;
                            else
                                return false;
                            }
                        </script>

                        <br class="clear">
			</div>
			<div style="clear: both;"></div>
                    <?php $url = plugin_dir_url(__FILE__); ?>


                </div>
		<h3><?php echo __('To print this video gallery into WordPress Post/Page use below code','responsive-filterable-portfolio');?></h3>
		<input type="text"
			value='[print_responsive_portfolio_plus_lightbox"] '
			style="width: 400px; height: 30px"
			onclick="this.focus(); this.select()" />
		<div class="clear"></div>
		<h3><?php echo __('To print this video gallery into WordPress theme/template PHP files use below code','responsive-filterable-portfolio');?></h3>
                <?php
		$shortcode = '[print_responsive_portfolio_plus_lightbox]';
		?>
                <input type="text"
			value="&lt;?php echo do_shortcode('<?php echo htmlentities($shortcode, ENT_QUOTES); ?>'); ?&gt;"
			style="width: 400px; height: 30px"
			onclick="this.focus(); this.select()" />
		<div class="clear"></div>
            </div>
         </div>
        <div id="postbox-container-1" class="postbox-container" > 

          <div class="postbox"> 
              <h3 class="hndle"><span></span><?php echo __('Access All Themes In One Price','responsive-filterable-portfolio');?></h3> 
              <div class="inside">
                  <center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank">
                          <img border="0" src="<?php echo plugins_url( 'images/300x250.gif', __FILE__ );?>" width="250" height="250">
                      </a></center>

                  <div style="margin:10px 5px">

                  </div>
              </div></div>
          <div class="postbox"> 
              <h3 class="hndle"><span></span><?php echo __('Speed Test For Your WP','responsive-filterable-portfolio');?></h3> 
              <div class="inside">
                  <center><a href="http://shareasale.com/r.cfm?b=875645&amp;u=675922&amp;m=41388&amp;urllink=&amp;afftrack=" target="_blank">
                          <img src="<?php echo plugins_url( 'images/300x250.png', __FILE__ );?>" width="250" height="250" border="0">
                      </a></center>
                  <div style="margin:10px 5px">
                  </div>
              </div></div>

      </div>      
    </div>
  </div>
<?php
	} 
        else if (strtolower ( $action ) == strtolower ( 'addedit' )) {
		$url = plugin_dir_url ( __FILE__ );
		
                $vNonce = wp_create_nonce('vNonce');
		?><?php
		if (isset ( $_POST ['btnsave'] )) {
			
                       if (!check_admin_referer('action_image_add_edit', 'add_edit_image_nonce')) {

                            wp_die('Security check fail');
                        }
			$uploads = wp_upload_dir ();
			$baseDir = $uploads ['basedir'];
			$baseDir = str_replace ( "\\", "/", $baseDir );
			$pathToImagesFolder = $baseDir . '/wp-best-portfolio';
			
                        if(isset($_POST['media_type']) and $_POST['media_type']=='video'){
                        
                        $media_type=trim($_POST['media_type']);    
			$vtype = trim ( htmlentities(strip_tags( $_POST ['vtype'] ),ENT_QUOTES) );
			$videourl = trim ( htmlentities(strip_tags($_POST ['videourl'] ),ENT_QUOTES));
			// echo $videourl;die;
			$vid = uniqid ( 'vid_' );
			$embed_url='';
			if ($vtype == 'youtube') {
				// parse
				
				$parseUrl = @parse_url ( $videourl );
				if (is_array ( $parseUrl )) {
					
					$queryStr = $parseUrl ['query'];
					parse_str ( $queryStr, $array );
					if (is_array ( $array ) and isset ( $array ['v'] )) {
						
						$vid = $array ['v'];
					}
				}
				
			    $embed_url="//www.youtube.com/embed/$vid";	
			}
			
			else if ($vtype == 'metacafe') {

                            $pattern = "#(?<=watch/).*?(?=/)#";
                            preg_match($pattern, $videourl, $matches, PREG_OFFSET_CAPTURE, 3);
                            $vid = 0;
                            if ($matches and is_array($matches)) {

                                $vid = $matches[0][0];
                            }


                            $embed_url = $videourl;
                            $embed_url=  str_replace("/watch/", "/embed/", $embed_url);

                        }
			
			
			
			$HdnMediaSelection = trim ( htmlentities(strip_tags($_POST ['HdnMediaSelection'] ),ENT_QUOTES));
			$videotitle = trim ( htmlentities(strip_tags($_POST ['title'] ),ENT_QUOTES)) ;
			$videotitleurl = trim ( htmlentities(strip_tags($_POST ['murl'] ),ENT_QUOTES));
			$mtag = trim (htmlentities(strip_tags( $_POST ['mtag'] ),ENT_QUOTES));
			
			
			$videotitle = str_replace("'","’",$videotitle);
			$videotitle = str_replace('"', '&quot;', $videotitle);
			
			
			$mtag = str_replace("'","’",$mtag);
			$mtag = str_replace('"', '&quot;', $mtag);
			
			
			if (isset ( $_POST ['open_link_in'] ))
				$open_link_in = 1;
			else
				$open_link_in = 0;
			
			$location = "admin.php?page=responsive_portfolio_with_lightbox_media_management";
				// edit save
			if (isset ( $_POST ['videoid'] )) {
				
				try {
						
						$videoidEdit=htmlentities(strip_tags($_POST ['videoid']),ENT_QUOTES);
						if (trim ( $_POST ['HdnMediaSelection'] ) != '') {
                                                    
							$pInfo = pathinfo ( $HdnMediaSelection );
							$ext = $pInfo ['extension'];
							$imagename = $vid . '_big.' . $ext;
							$imageUploadTo = $pathToImagesFolder . '/' . $imagename;
                                                        
                                                   	@copy ( $HdnMediaSelection, $imageUploadTo );
                                                        if(!file_exists($imageUploadTo)){
                                                         rfp_save_image_curl($HdnMediaSelection,$imageUploadTo);
                                                        }
                                                        
                                                        $settings = get_option( 'best_portfolio_grid_settings' );
						        $imageheight = $settings ['imageheight'];
                                                        $imagewidth = $settings ['imagewidth'];
                                                        @unlink($pathToImagesFolder.'/'.$vid . '_big_'.$imageheight.'_'.$imagewidth.'.'.$ext);
						}
							
						$query = "update " . $wpdb->prefix . "e_portfolio
						set media_type='$media_type', vtype='$vtype',vid='$vid',murl='$videourl',embed_url='$embed_url',image_name='$imagename',HdnMediaSelection='$HdnMediaSelection',
						title='$videotitle',videourl='$videotitleurl',mtag='$mtag',
						open_link_in=$open_link_in  where id=$videoidEdit";
							
						//echo $query;die;
						$wpdb->query ( $query );
							
						$wp_best_portfolio_msg = array ();
						$wp_best_portfolio_msg ['type'] = 'succ';
						$wp_best_portfolio_msg ['message'] =  __('Video updated successfully.','responsive-filterable-portfolio');
						update_option ( 'wp_best_portfolio_msg', $wp_best_portfolio_msg );
					} catch ( Exception $e ) {
							
						$wp_best_portfolio_msg = array ();
										$wp_best_portfolio_msg ['type'] = 'err';
										$wp_best_portfolio_msg ['message'] = __('Error while adding video','responsive-filterable-portfolio');
						update_option ( 'wp_best_portfolio_msg', $wp_best_portfolio_msg );
						}

				
				
				echo "<script type='text/javascript'> location.href='$location';</script>";
				exit ();
			} else {
				
				$createdOn = date ( 'Y-m-d h:i:s' );
                                if (function_exists ( 'date_i18n' )) {

                                        $createdOn = date_i18n ( 'Y-m-d' . ' ' . get_option ( 'time_format' ), false, false );
                                        if (get_option ( 'time_format' ) == 'H:i')
                                                $createdOn = date ( 'Y-m-d H:i:s', strtotime ( $createdOn ) );
                                        else
                                                $createdOn = date ( 'Y-m-d h:i:s', strtotime ( $createdOn ) );
                                }
				
				try {
					
					if (trim ( $_POST ['HdnMediaSelection'] ) != '') {
						$pInfo = pathinfo ( $HdnMediaSelection );
						$ext = $pInfo ['extension'];
						$imagename = $vid . '_big.' . $ext;
						$imageUploadTo = $pathToImagesFolder . '/' . $imagename;
						@copy ( $HdnMediaSelection, $imageUploadTo );
                                                if(!file_exists($imageUploadTo)){
                                                  rfp_save_image_curl($HdnMediaSelection,$imageUploadTo);
                                                }
					}
					
					$query = "INSERT INTO " . $wpdb->prefix . "e_portfolio 
                                		(media_type,image_name,title,murl,mtag,open_link_in,
                                                vtype,vid,videourl,embed_url,HdnMediaSelection,createdon) 
                                                VALUES ('$media_type','$imagename','$videotitle','$videourl','$mtag',
                                                        $open_link_in,'$vtype','$vid','$videourl','$embed_url','$HdnMediaSelection', '$createdOn')";

					
					$wpdb->query ( $query );
					
					$wp_best_portfolio_msg = array ();
					$wp_best_portfolio_msg ['type'] = 'succ';
					$wp_best_portfolio_msg ['message'] = __('New video added successfully.','responsive-filterable-portfolio');
					update_option ( 'wp_best_portfolio_msg', $wp_best_portfolio_msg );
				} catch ( Exception $e ) {
					
					$wp_best_portfolio_msg = array ();
					$wp_best_portfolio_msg ['type'] = 'err';
					$wp_best_portfolio_msg ['message'] = __('Error while adding video','responsive-filterable-portfolio');
					update_option ( 'wp_best_portfolio_msg', $wp_best_portfolio_msg );
				}
				
				echo "<script type='text/javascript'> location.href='$location';</script>";
				exit ();
			}
                        
                   }
                   else if(isset($_POST['media_type']) and $_POST['media_type']=='image'){
                            
                        $vid = uniqid ( 'vid_' );
                        $media_type=trim(htmlentities(strip_tags($_POST['media_type']),ENT_QUOTES));   
                        $HdnMediaSelection = trim ( htmlentities(strip_tags($_POST ['HdnMediaSelection_image'] ),ENT_QUOTES));
			$mtitle = trim ( htmlentities(strip_tags($_POST ['title'] ),ENT_QUOTES)) ;
			$murl = trim ( htmlentities(strip_tags($_POST ['murl'] ),ENT_QUOTES));
			$mtag = trim ( htmlentities(strip_tags($_POST ['mtag'] ),ENT_QUOTES));
                        
               	
			
			
			$mtitle = str_replace("'","’",$mtitle);
			$mtitle = str_replace('"', '&quot;', $mtitle);
			
			
			$mtag = str_replace("'","’",$mtag);
			$mtag = str_replace('"', '&quot;', $mtag);
			
			
			if (isset ( $_POST ['open_link_in'] ))
				$open_link_in = 1;
			else
				$open_link_in = 0;
			
			

			$location = "admin.php?page=responsive_portfolio_with_lightbox_media_management";
				// edit save
			if (isset ( $_POST ['imageid'] )) {
				
				try {
						
						$videoidEdit=htmlentities(strip_tags($_POST ['imageid']),ENT_QUOTES);
						if (trim ( $_POST ['HdnMediaSelection_image'] ) != '') {
                                                    
							$pInfo = pathinfo ( $HdnMediaSelection );
							$ext = $pInfo ['extension'];
							$imagename = $vid . '_big.' . $ext;
							$imageUploadTo = $pathToImagesFolder . '/' . $imagename;
                                                        
                                                   	@copy ( $HdnMediaSelection, $imageUploadTo );
                                                        if(!file_exists($imageUploadTo)){
                                                         rfp_save_image_curl($HdnMediaSelection,$imageUploadTo);
                                                        }
                                                        
                                                        $settings = get_option( 'best_portfolio_grid_settings' );
						        $imageheight = $settings ['imageheight'];
                                                        $imagewidth = $settings ['imagewidth'];
                                                        @unlink($pathToImagesFolder.'/'.$vid . '_big_'.$imageheight.'_'.$imagewidth.'.'.$ext);
						}
							
						$query = "update " . $wpdb->prefix . "e_portfolio
						set media_type='$media_type', murl='$murl',image_name='$imagename',HdnMediaSelection='$HdnMediaSelection',
						title='$mtitle',mtag='$mtag',
						open_link_in=$open_link_in  where id=$videoidEdit";
							
						//echo $query;die;
						$wpdb->query ( $query );
							
						$wp_best_portfolio_msg = array ();
						$wp_best_portfolio_msg ['type'] = 'succ';
						$wp_best_portfolio_msg ['message'] = __('Image updated successfully.','responsive-filterable-portfolio');
						update_option ( 'wp_best_portfolio_msg', $wp_best_portfolio_msg );
					} catch ( Exception $e ) {
							
						$wp_best_portfolio_msg = array ();
										$wp_best_portfolio_msg ['type'] = 'err';
										$wp_best_portfolio_msg ['message'] = __('Error while adding image','responsive-filterable-portfolio');
						update_option ( 'wp_best_portfolio_msg', $wp_best_portfolio_msg );
						}

				
				
				echo "<script type='text/javascript'> location.href='$location';</script>";
				exit ();
			} else {
				
				$createdOn = date ( 'Y-m-d h:i:s' );
                                if (function_exists ( 'date_i18n' )) {

                                        $createdOn = date_i18n ( 'Y-m-d' . ' ' . get_option ( 'time_format' ), false, false );
                                        if (get_option ( 'time_format' ) == 'H:i')
                                                $createdOn = date ( 'Y-m-d H:i:s', strtotime ( $createdOn ) );
                                        else
                                                $createdOn = date ( 'Y-m-d h:i:s', strtotime ( $createdOn ) );
                                }
				
				try {
					
					if (trim ( $_POST ['HdnMediaSelection_image'] ) != '') {
						$pInfo = pathinfo ( $HdnMediaSelection );
						$ext = $pInfo ['extension'];
						$imagename = $vid . '_big.' . $ext;
						$imageUploadTo = $pathToImagesFolder . '/' . $imagename;
						@copy ( $HdnMediaSelection, $imageUploadTo );
                                                if(!file_exists($imageUploadTo)){
                                                    rfp_save_image_curl($HdnMediaSelection,$imageUploadTo);
                                                 }
					}
					
					$query = "INSERT INTO " . $wpdb->prefix . "e_portfolio 
                                		(media_type,image_name,title,murl,mtag,open_link_in,
                                                HdnMediaSelection,createdon) 
                                                VALUES ('$media_type','$imagename','$mtitle','$murl','$mtag',
                                                        $open_link_in,'$HdnMediaSelection', '$createdOn')";

					
					$wpdb->query ( $query );
                                        
                                        //echo $wpdb->last_error;die;
					
					$wp_best_portfolio_msg = array ();
					$wp_best_portfolio_msg ['type'] = 'succ';
					$wp_best_portfolio_msg ['message'] = __('New image added successfully.','responsive-filterable-portfolio');
					update_option ( 'wp_best_portfolio_msg', $wp_best_portfolio_msg );
				} catch ( Exception $e ) {
					
					$wp_best_portfolio_msg = array ();
					$wp_best_portfolio_msg ['type'] = 'err';
					$wp_best_portfolio_msg ['message'] = __('Error while adding image','responsive-filterable-portfolio');
					update_option ( 'wp_best_portfolio_msg', $wp_best_portfolio_msg );
				}
				
				echo "<script type='text/javascript'> location.href='$location';</script>";
				exit ();
			}
                       
                       
                   }
                   else if(isset($_POST['media_type']) and $_POST['media_type']=='link'){
                            
                        $vid = uniqid ( 'vid_' );
                        $media_type=trim(htmlentities(strip_tags($_POST['media_type']),ENT_QUOTES));   
                        $HdnMediaSelection = trim (htmlentities(strip_tags( $_POST ['HdnMediaSelection_link'] ),ENT_QUOTES));
			$mtitle = trim (htmlentities(strip_tags( $_POST ['title'] ),ENT_QUOTES));
			
			$mtag = trim ( htmlentities(strip_tags($_POST ['mtag'] ),ENT_QUOTES));
			$murl = trim ( htmlentities(strip_tags($_POST ['murl'] ),ENT_QUOTES));
                        
               		
			$mtitle = str_replace("'","’",$mtitle);
			$mtitle = str_replace('"', '&quot;', $mtitle);
			
			
			$mtag = str_replace("'","’",$mtag);
			$mtag = str_replace('"', '&quot;', $mtag);
			
			
			if (isset ( $_POST ['open_link_in'] ))
				$open_link_in = 1;
			else
				$open_link_in = 0;
			
			

			$location = "admin.php?page=responsive_portfolio_with_lightbox_media_management";
				// edit save
			if (isset ( $_POST ['linkid'] )) {
				
				try {
						
						$videoidEdit=htmlentities(strip_tags($_POST ['linkid']),ENT_QUOTES);
						if (trim ( $_POST ['HdnMediaSelection_link'] ) != '') {
                                                    
							$pInfo = pathinfo ( $HdnMediaSelection );
							$ext = $pInfo ['extension'];
							$imagename = $vid . '_big.' . $ext;
							$imageUploadTo = $pathToImagesFolder . '/' . $imagename;
                                                        
                                                   	@copy ( $HdnMediaSelection, $imageUploadTo );
                                                        if(!file_exists($imageUploadTo)){
                                                         rfp_save_image_curl($HdnMediaSelection,$imageUploadTo);
                                                        }
                                                        
                                                       
                                                        $settings = get_option( 'best_portfolio_grid_settings' );
						        $imageheight = $settings ['imageheight'];
                                                        $imagewidth = $settings ['imagewidth'];
                                                        @unlink($pathToImagesFolder.'/'.$vid . '_big_'.$imageheight.'_'.$imagewidth.'.'.$ext);
						}
							
						$query = "update " . $wpdb->prefix . "e_portfolio
						set media_type='$media_type', murl='$murl',image_name='$imagename',HdnMediaSelection='$HdnMediaSelection',
						title='$mtitle',mtag='$mtag',
						open_link_in=$open_link_in  where id=$videoidEdit";
							
						//echo $query;die;
						$wpdb->query ( $query );
							
						$wp_best_portfolio_msg = array ();
						$wp_best_portfolio_msg ['type'] = 'succ';
						$wp_best_portfolio_msg ['message'] = __('Link updated successfully.','responsive-filterable-portfolio');
						update_option ( 'wp_best_portfolio_msg', $wp_best_portfolio_msg );
					} catch ( Exception $e ) {
							
						$wp_best_portfolio_msg = array ();
										$wp_best_portfolio_msg ['type'] = 'err';
										$wp_best_portfolio_msg ['message'] = __('Error while adding link','responsive-filterable-portfolio');
						update_option ( 'wp_best_portfolio_msg', $wp_best_portfolio_msg );
						}

				
				
				echo "<script type='text/javascript'> location.href='$location';</script>";
				exit ();
			} else {
				
				$createdOn = date ( 'Y-m-d h:i:s' );
                                if (function_exists ( 'date_i18n' )) {

                                        $createdOn = date_i18n ( 'Y-m-d' . ' ' . get_option ( 'time_format' ), false, false );
                                        if (get_option ( 'time_format' ) == 'H:i')
                                                $createdOn = date ( 'Y-m-d H:i:s', strtotime ( $createdOn ) );
                                        else
                                                $createdOn = date ( 'Y-m-d h:i:s', strtotime ( $createdOn ) );
                                }
				
				try {
					
					if (trim ( $_POST ['HdnMediaSelection_link'] ) != '') {
						$pInfo = pathinfo ( $HdnMediaSelection );
						$ext = $pInfo ['extension'];
						$imagename = $vid . '_big.' . $ext;
						$imageUploadTo = $pathToImagesFolder . '/' . $imagename;
						@copy ( $HdnMediaSelection, $imageUploadTo );
                                                if(!file_exists($imageUploadTo)){
                                                    rfp_save_image_curl($HdnMediaSelection,$imageUploadTo);
                                                   }
					}
					
					$query = "INSERT INTO " . $wpdb->prefix . "e_portfolio 
                                		(media_type,image_name,title,murl,mtag,open_link_in,
                                                HdnMediaSelection,createdon) 
                                                VALUES ('$media_type','$imagename','$mtitle','$murl','$mtag',
                                                        $open_link_in,'$HdnMediaSelection', '$createdOn')";

				
					$wpdb->query ( $query );
					
					$wp_best_portfolio_msg = array ();
					$wp_best_portfolio_msg ['type'] = 'succ';
					$wp_best_portfolio_msg ['message'] = __('New link added successfully.','responsive-filterable-portfolio');
					update_option ( 'wp_best_portfolio_msg', $wp_best_portfolio_msg );
				} catch ( Exception $e ) {
					
					$wp_best_portfolio_msg = array ();
					$wp_best_portfolio_msg ['type'] = 'err';
					$wp_best_portfolio_msg ['message'] = __('Error while adding link','responsive-filterable-portfolio');
					update_option ( 'wp_best_portfolio_msg', $wp_best_portfolio_msg );
				}
				
				echo "<script type='text/javascript'> location.href='$location';</script>";
				exit ();
			}
                       
                       
                   }
                   
                   
		} else {
			
			$uploads = wp_upload_dir ();
			$baseurl = $uploads ['baseurl'];
			$baseurl .= '/wp-best-portfolio/';
			?>
          <div id="poststuff" > 
                  <div id="post-body" class="metabox-holder columns-2" >  
                   <div id="post-body-content">
                       <table><tr><td><a href="https://twitter.com/FreeAdsPost" class="twitter-follow-button" data-show-count="false" data-size="large" data-show-screen-name="false">Follow @FreeAdsPost</a>
                                    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></td>
                                <td>
                                    <a target="_blank" title="Donate" href="http://www.i13websolution.com/donate-wordpress_image_thumbnail.php">
                                        <img id="help us for free plugin" height="30" width="90" src="<?php echo plugins_url( 'images/paypaldonate.jpg', __FILE__ ) ;?>" border="0" alt="help us for free plugin" title="help us for free plugin">
                                    </a>
                                </td>
                            </tr>
                        </table>
                         <span><h3 style="color: blue;"><a target="_blank" href="http://www.i13websolution.com/wordpress-responsive-media-portfolio-grid.html"><?php echo __('UPGRADE TO PRO VERSION','responsive-filterable-portfolio');?></a></h3></span>
                
                         <div class="wrap">
	    	<?php
		    	if (isset ( $_GET ['id'] ) and $_GET ['id'] > 0) {
				
				$id = $_GET ['id'];
				
				$query = "SELECT * FROM " . $wpdb->prefix . "e_portfolio WHERE id=$id";
				
				$myrow = $wpdb->get_row ( $query );
				
				if (is_object ( $myrow )) {
					
					$media_type =  $myrow->media_type ;
					$vtype =  $myrow->vtype ;
					$title =  $myrow->title ;
					$murl =   $myrow->murl;
					$mtag =  $myrow->mtag ;
					$image_name = $myrow->image_name;
					$video_url = $myrow->murl ;
					$HdnMediaSelection = $myrow->HdnMediaSelection;
					$videotitle = $myrow->title;
					$videotitleurl=$myrow->videourl;
					$open_link_in = $myrow->open_link_in;
					
				}
				?>
	         <h2><?php echo __('Update Media','responsive-filterable-portfolio');?></h2><?php
			} else {
				
				$media_type = '';
                                $vtype = '';
                                $murl = '';
                                $title = '';
                                $mtag = '';
                                $image_name = '';
                                $video_url = '';
                                $HdnMediaSelection = '';
                                $videotitle = '';
                                $videotitleurl='';
                                $open_link_in = '';
                              
				
				?>
                 <h2><?php echo __('Add Media','responsive-filterable-portfolio');?></h2>
                   <?php } ?>
                   <br />
					<div id="poststuff">
						<div id="post-body" class="metabox-holder columns-2">
							<div id="post-body-content">
                                                            
                                                            <div class="stuffbox" id="mediatype" style="width: 100%">
                                                                            <h3>
                                                                                    <label for="link_name"><?php echo __('Media Type','responsive-filterable-portfolio');?> (<span
                                                                                            style="font-size: 11px; font-weight: normal"><?php echo __('Choose Media Type','responsive-filterable-portfolio');?></span>)
                                                                                    </label>
                                                                            </h3>
                                                                            <div class="inside">
                                                                                    <div>
                                                                                            <input type="radio" value="image" name="media_type_p" <?php if($media_type=='image'): ?> checked='checked' <?php endif;?> style="width: 15px" id="type_image" /><?php echo __('Image','responsive-filterable-portfolio');?>&nbsp;&nbsp;
                                                                                            <input type="radio" value="video" name="media_type_p" <?php if($media_type=='video'): ?> checked='checked' <?php endif;?> style="width: 15px" id="type_video" /><?php echo __('Video','responsive-filterable-portfolio');?>&nbsp;&nbsp; 
                                                                                            <input <?php if($media_type=='link'): ?> checked='checked' <?php endif;?> type="radio" value="link" name="media_type_p" style="width: 15px" id="type_link" /><?php echo __('Link','responsive-filterable-portfolio');?>&nbsp;&nbsp;

                                                                                    </div>
                                                                                    <div style="clear: both"></div>
                                                                                    <div></div>
                                                                                    <div style="clear: both"></div>
                                                                                    <br />

                                                                            </div>

                                                                        <script>
                                                                           var $n = jQuery.noConflict();   
                                                                           
                                                                           $n(document).ready(function() {
                                                                                 $n("input[name = 'media_type_p']").click(function(){
                                                                                    var radioValue = $n("input[name='media_type_p']:checked").val();
                                                                                    if(radioValue=='video'){

                                                                                        $n("#addvideo").show(500);
                                                                                        $n("#addimage_").hide(500);
                                                                                        $n("#addlink_").hide(500);
                                                                                      
                                                                                    }
                                                                                    else if(radioValue=='image'){

                                                                                       $n("#addvideo").hide(500);
                                                                                       $n("#addimage_").show(500);
                                                                                       $n("#addlink_").hide(500);
                                                                                    }
                                                                                    else if(radioValue=='link'){

                                                                                      $n("#addlink_").show(500);
                                                                                      $n("#addvideo").hide(500);
                                                                                      $n("#addimage_").hide(500);

                                                                                    }

                                                                                });
                                                                                
                                                                                 <?php if(isset($_GET['id']) and (int) $_GET['id']>0):?>
                                                                                
                                                                                    <?php if($media_type=='video') :?>
                                                                                        $n("#type_video").trigger('click');
                                                                                    <?php elseif($media_type=='image'):?>
                                                                                        $n("#type_image").trigger('click');
                                                                                    <?php elseif($media_type=='link'):?>
                                                                                        $n("#type_link").trigger('click');
                                                                                    <?php endif;?>    

                                                                                 <?php endif;?>  
                                                                                
                                                                             });   
                                                                             
                                                                              
                                                                         </script>       
                                                                    </div>
                                                                    <form method="post" action="" id="addvideo" name="addvideo" enctype="multipart/form-data" style="display:none">
                                                                    
                                                                        <input type="hidden" name="media_type" id="media_type" value="video" />
									<div class="stuffbox" id="videoinfo_div_1" style="width: 100%;">
										<h3>
											<label for="link_name"><?php echo __('Video Information','responsive-filterable-portfolio');?> (<span
												style="font-size: 11px; font-weight: normal"><?php echo __('Choose Video Site','responsive-filterable-portfolio');?></span>)
											</label>
										</h3>
										<div class="inside">
											<div>
												<input type="radio" value="youtube" name="vtype"
													<?php if($vtype=='youtube'): ?> checked='checked' <?php endif;?> style="width: 15px" id="type_youtube" /><?php echo __('Youtube','responsive-filterable-portfolio');?>&nbsp;&nbsp;
													<input <?php if($vtype=='metacafe'): ?> checked='checked' <?php endif;?> type="radio" value="metacafe" name="vtype"
													style="width: 15px" id="type_metacafe" /><?php echo __('Metacafe','responsive-filterable-portfolio');?>&nbsp;&nbsp;
												
											</div>
											<div style="clear: both"></div>
											<div></div>
											<div style="clear: both"></div>
											<br />
											<div>
                                                                                            <b><?php echo __('Video Url','responsive-filterable-portfolio');?></b> <input style="width:98%" type="text" id="videourl" class="url" tabindex="1"  name="videourl" value="<?php echo $video_url; ?>">
											</div>
											<div style="clear: both"></div>
											<div></div>
											<div style="clear: both"></div>
										</div>
									</div>
									<div class="stuffbox" id="videoinfo_div_2" style="width: 100%;">
										<h3>
											<label for="link_name"><?php echo __('Video Thumbnail Information','responsive-filterable-portfolio');?></label>
										</h3>
										<div class="inside" id="fileuploaddiv">
                                                                                <?php if ($image_name != "") { ?>
                                                                                        <div>
												<b><?php echo __('Current Image','responsive-filterable-portfolio');?>: </b>
												<br/>
												<img id="img_disp" name="img_disp"
													src="<?php echo $baseurl . $image_name; ?>" />
											</div>
                                                                                <?php }else{ ?>      
                                                                                            <img
												src="<?php echo plugins_url('/images/no-img.jpeg', __FILE__); ?>"
												id="img_disp" name="img_disp" />
                                                           
                                                                                     <?php } ?>
                                                                                         <br /> <a
												href="javascript:;" class="niks_media"
												id="videoFromExternalSite"  ><b><?php echo __('Click Here to get video information and thumbnail','responsive-filterable-portfolio');?><span id='fromval'> <?php echo __('From','responsive-filterable-portfolio');?> <?php echo $vtype;?></span>
											</b></a>&nbsp;<img
												src="<?php echo plugins_url('/images/ajax-loader.gif', __FILE__); ?>"
												style="display: none" id="loading_img" name="loading_img" />
											<div style="clear: both"></div>
											<div></div>
											<div class="uploader">
												<br /> <b style="margin-left: 50px;">OR</b>
												<div style="clear: both; margin-top: 15px;"></div>
                                                                                                
                                                                                                        <a
													href="javascript:;" class="niks_media" id="myMediaUploader"><b><?php echo __('Click Here to upload custom video thumbnail','responsive-filterable-portfolio');?></b></a>
                                              
                                                                                                    <br /> <br />
												<div>
													<input id="HdnMediaSelection" name="HdnMediaSelection"
														type="hidden" value="<?php echo $HdnMediaSelection;?>" />
												</div>
												<div style="clear: both"></div>
												<div></div>
												<div style="clear: both"></div>

												<br />
											</div>
                                                                                <script>
                                                                                        
                                                                                   
                                                                                    function GetParameterValues(param,str) {
                                                                                      var return_p='';  
                                                                                      var url = str.slice(str.indexOf('?') + 1).split('&');
                                                                                      for (var i = 0; i < url.length; i++) {
                                                                                            var urlparam = url[i].split('=');
                                                                                            if (urlparam[0] == param) {
                                                                                             return_p= urlparam[1];
                                                                                            }
                                                                                        }
                                                                                        return return_p;
                                                                                    }

                                                                                   

                                                                                    function UrlExists(url, cb){
                                                                                        $n.ajax({
                                                                                            url:      url,
                                                                                            dataType: 'text',
                                                                                            type:     'GET',
                                                                                            complete:  function(xhr){
                                                                                                if(typeof cb === 'function')
                                                                                                   cb.apply(this, [xhr.status]);
                                                                                            }
                                                                                        });
                                                                                    }

                                                                                   


                                                                                    $n(document).ready(function() {


                                                                                    $n("input:radio[name=vtype]").click(function() {


                                                                                    var value = $n(this).val();
                                                                                            $n("#fromval").html(" from " + value);
                                                                                    });

                                                                                    $n("#videoFromExternalSite").click(function() {


                                                                                    var videoService = $n('input[name="vtype"]:checked').length;
                                                                                            var videourlVal = $n.trim($n("#videourl").val());
                                                                                            var flag = true;
                                                                                            if (videourlVal == '' && videoService == 0){

                                                                                    alert('Please select video site.\nPlease enter video url.');
                                                                                            $n("input:radio[name=vtype]").focus();
                                                                                            flag = false;

                                                                                    }
                                                                                    else if (videoService == 0){

                                                                                    alert('Please select video site.');
                                                                                            $n("input:radio[name=vtype]").focus();
                                                                                            flag = false;
                                                                                    }
                                                                                    else if (videourlVal == ''){

                                                                                    alert('Please enter video url.');
                                                                                            $n("#videourl").focus();
                                                                                            flag = false;
                                                                                    }

                                                                                    if (flag){

                                                                                        setTimeout(function() {
                                                                                                 $n("#loading_img").show();   
                                                                                                }, 100);

                                                                                            var selectedRadio = $n('input[name=vtype]');
                                                                                            var checkedValueRadio = selectedRadio.filter(':checked').val();
                                                                                            if (checkedValueRadio == 'youtube') {
                                                                                            var vId = GetParameterValues('v', videourlVal);
                                                                                            if(vId!=''){


                                                                                             var tumbnailImg='http://img.youtube.com/vi/'+vId+'/maxresdefault.jpg';

                                                                                             var data = {
                                                                                                                'action': 'rfp_check_file_exist_portfolio',
                                                                                                                'url': tumbnailImg,
                                                                                                                'vNonce':'<?php echo $vNonce; ?>'
                                                                                                        };

                                                                                                        $n.post(ajaxurl, data, function(response) {



                                                                                                      var youtubeJsonUri='http://www.youtube.com/oembed?url=https://www.youtube.com/watch%3Fv='+vId+'&format=json';
                                                                                                       var data_youtube = {
                                                                                                                'action': 'rfp_get_youtube_info_portfolio',
                                                                                                                'url': youtubeJsonUri,
                                                                                                                'vid':vId,
                                                                                                                 'vNonce':'<?php echo $vNonce; ?>'
                                                                                                        };

                                                                                                      $n.post(ajaxurl, data_youtube, function(data) {

                                                                                                       data = $n.parseJSON(data);

                                                                                                       if(typeof data =='object'){    
                                                                                                               if(typeof data =='object'){ 

                                                                                                                    if(data.title!='' && data.title!=''){
                                                                                                                        $n("#title").val(data.title); 
                                                                                                                    }
                                                                                                                    $n("#murl").val(videourlVal);
                                                                                                                   
                                                                                                                    if(response=='404' && data.thumbnail_url!=''){
                                                                                                                         tumbnailImg=data.thumbnail_url;
                                                                                                                    }
                                                                                                                    else{
                                                                                                                         tumbnailImg='http://img.youtube.com/vi/'+vId+'/0.jpg';
                                                                                                                     }

                                                                                                                    $n("#img_disp").attr('src', tumbnailImg);
                                                                                                                    $n("#HdnMediaSelection").val(tumbnailImg);
                                                                                                                    $n("#loading_img").hide();

                                                                                                               }

                                                                                                            }
                                                                                                           $n("#loading_img").hide();
                                                                                                       })  


                                                                                                        });

                                                                                            }
                                                                                            else{
                                                                                                alert('Could not found such video');
                                                                                                $n("#loading_img").hide();
                                                                                            }
                                                                                        }
                                                                                        
                                                                                        else if(checkedValueRadio == 'metacafe'){

                                                                                                 $n("#loading_img").show();
                                                                                                 var data = {
                                                                                                                'action': 'rfp_get_metacafe_info_portfolio',
                                                                                                                'url': videourlVal,
                                                                                                                'vNonce':'<?php echo $vNonce; ?>'
                                                                                                        };

                                                                                                        $n.post(ajaxurl, data,function(response) {

                                                                                                                obj = $n.parseJSON(response);	
                                                                                                            $n("#HdnMediaSelection").val(obj.HdnMediaSelection);
                                                                                                            $n("#title").val($n.trim(obj.videotitle));
                                                                                                            $n("#murl").val(obj.videotitleurl);
                                                                                                            $n("#img_disp").attr('src', obj.HdnMediaSelection);
                                                                                                            $n("#loading_img").hide();
                                                                                                });	  


                                                                                        } 
                                                                                                  

                                                                                        $n("#loading_img").hide();
                                                                                    }

                                                                                     setTimeout(function() {
                                                                                                 $n("#loading_img").hide();   
                                                                                         }, 2000);

                                                                                    });
                                                                                            //uploading files variable
                                                                                       var custom_file_frame;
                                                                                  $n("#myMediaUploader").click(function(event) {
                                                                                    event.preventDefault();
                                                                                            //If the frame already exists, reopen it
                                                                                            if (typeof (custom_file_frame) !== "undefined") {
                                                                                    custom_file_frame.close();
                                                                                    }

                                                                                    //Create WP media frame.
                                                                                    custom_file_frame = wp.media.frames.customHeader = wp.media({
                                                                                    //Title of media manager frame
                                                                                    title: "WP Media Uploader",
                                                                                            library: {
                                                                                            type: 'image'
                                                                                            },
                                                                                            button: {
                                                                                            //Button text
                                                                                            text: "Set Image"
                                                                                            },
                                                                                            //Do not allow multiple files, if you want multiple, set true
                                                                                            multiple: false
                                                                                    });
                                                                                            //callback for selected image
                                                                                            custom_file_frame.on('select', function() {

                                                                                        var attachment = custom_file_frame.state().get('selection').first().toJSON();
                                                                                        var validExtensions = new Array();
                                                                                        validExtensions[0] = 'jpg';
                                                                                        validExtensions[1] = 'jpeg';
                                                                                        validExtensions[2] = 'png';
                                                                                        validExtensions[3] = 'gif';
                                                                                       
                                                                                        var inarr = parseInt($n.inArray(attachment.subtype, validExtensions));
                                                                                          if (inarr > 0 && attachment.type.toLowerCase() == 'image'){

                                                                                            var titleTouse = "";
                                                                                           
                                                                                             if ($n.trim(attachment.title) != ''){

                                                                                                 titleTouse = $n.trim(attachment.title);
                                                                                            }
                                                                                            else if ($n.trim(attachment.caption) != ''){

                                                                                                titleTouse = $n.trim(attachment.caption);
                                                                                            }

                                                                                            
                                                                                           // $n("#videotitle").val(titleTouse);
                                                                                         
                                                                                            if (attachment.id != ''){

                                                                                                      $n("#HdnMediaSelection").val(attachment.url);
                                                                                                      $n("#img_disp").attr('src', attachment.url);

                                                                                                }

                                                                                            }
                                                                                            else{

                                                                                              alert('Invalid image selection.');
                                                                                            }
                                                                                            //do something with attachment variable, for example attachment.filename
                                                                                            //Object:
                                                                                            //attachment.alt - image alt
                                                                                            //attachment.author - author id
                                                                                            //attachment.caption
                                                                                            //attachment.dateFormatted - date of image uploaded
                                                                                            //attachment.description
                                                                                            //attachment.editLink - edit link of media
                                                                                            //attachment.filename
                                                                                            //attachment.height
                                                                                            //attachment.icon - don't know WTF?))
                                                                                            //attachment.id - id of attachment
                                                                                            //attachment.link - public link of attachment, for example ""http://site.com/?attachment_id=115""
                                                                                            //attachment.menuOrder
                                                                                            //attachment.mime - mime type, for example image/jpeg"
                                                                                            //attachment.name - name of attachment file, for example "my-image"
                                                                                            //attachment.status - usual is "inherit"
                                                                                            //attachment.subtype - "jpeg" if is "jpg"
                                                                                            //attachment.title
                                                                                            //attachment.type - "image"
                                                                                            //attachment.uploadedTo
                                                                                            //attachment.url - http url of image, for example "http://site.com/wp-content/uploads/2012/12/my-image.jpg"
                                                                                            //attachment.width
                                                                                            });
                                                                                            //Open modal
                                                                                            custom_file_frame.open();
                                                                                      });
                                                                                    })
                                                                                 </script>
										</div>
									</div>
                                                                        <div class="stuffbox" id="namediv" style="width: 100%">
										<h3>
											<label for="link_name">Video Title (<span
												style="font-size: 11px; font-weight: normal"><?php echo __('Used into lightbox','responsive-filterable-portfolio');?></span>)
											</label>
										</h3>
										<div class="inside">
											<div>
												<input type="text" id="title" tabindex="1" size="30" name="title" value="<?php echo $videotitle; ?>">
											</div>
											<div style="clear: both"></div>
											<div></div>
											<div style="clear: both"></div>
										</div>
									</div>
									<div class="stuffbox" id="namediv" style="width: 100%">
										<h3>
											<label for="link_name"><?php echo __('Video Title Url','responsive-filterable-portfolio');?> (<span
												style="font-size: 11px; font-weight: normal"><?php _e(' click on title redirect to this url.Used in lightbox for video title'); ?></span>)
											</label>
										</h3>
										<div class="inside">
											<div>
												<input type="text" id="murl" class="url"
													tabindex="1" size="30" name="murl"
													value="<?php echo $videotitleurl; ?>">
											</div>
											<div style="clear: both"></div>
											<div></div>
											<div style="clear: both"></div>

										</div>
									</div>
									<div class="stuffbox" id="namediv" style="width: 100%">
										<h3>
											<label for="mtag"><?php echo __('Media Tags','responsive-filterable-portfolio');?> (<span
												style="font-size: 11px; font-weight: normal"><?php echo __('Tags used for filter on grid.You can add comma separated values.','responsive-filterable-portfolio');?></span>)
											</label>
										</h3>
										<div class="inside">
											<div>
												<input type="text" id="mtag" 
													tabindex="1" size="30" name="mtag"
													value="<?php echo $mtag; ?>">
											</div>
											<div style="clear: both"></div>
											<div></div>
											<div style="clear: both"></div>

										</div>
									</div>
									<div class="stuffbox" id="namediv" style="width: 100%">
										<h3>
											<label><?php echo __('Play Video In Lightbox?','responsive-filterable-portfolio');?> (<span
												style="font-size: 11px; font-weight: normal"><?php echo __('show video in lightbox or redirect?','responsive-filterable-portfolio');?></span>)
											</label>
										</h3>
										<div class="inside">
											<table>
												<tr>
													<td>
														<div>
															<input type="checkbox" id="open_link_in" size="30"
																name="open_link_in" value=""
																<?php
																	if ($open_link_in == true) {
																		echo "checked='checked'";
																	}
																	?>
																style="width: 20px;">&nbsp;<?php echo __('Show Video In Lightbox?','responsive-filterable-portfolio');?>
														</div>
														<div style="clear: both"></div>
														<div></div>
													</td>
												</tr>
											</table>
											<div style="clear: both"></div>
										</div>
									</div>
									
									
                                                                        <?php if (isset($_GET['id']) and (int) $_GET['id'] > 0) { ?> 
										 <input type="hidden" name="videoid" id="videoid" value="<?php echo (int) $_GET['id']; ?>">
                                                                         <?php
										}
										?>
                                                                            <?php wp_nonce_field('action_image_add_edit', 'add_edit_image_nonce'); ?>      
                                                                            <input type="submit"
										onclick="" name="btnsave" id="btnsave" value="<?php echo __('Save Changes','responsive-filterable-portfolio');?>"
										class="button-primary">&nbsp;&nbsp;<input type="button"
										name="cancle" id="cancle" value="<?php echo __('Cancel','responsive-filterable-portfolio');?>"
										class="button-primary"
										onclick="location.href = 'admin.php?page=responsive_portfolio_with_lightbox_media_management'">

								</form>
                                                                   <form method="post" action="" id="addimage_" name="addimage_" enctype="multipart/form-data" style="display:none">
                                                                    
                                                                        <input type="hidden" name="media_type" id="media_type" value="image" />
									 <div class="stuffbox" id="image_info" style="width: 100%;">
										<h3>
											<label for="link_name"><?php echo __('Image Information','responsive-filterable-portfolio');?></label>
										</h3>
										<div class="inside" id="fileuploaddiv">
                                                                                <?php if ($image_name != "") { ?>
                                                                                        <div>
												<b><?php echo __('Current Image','responsive-filterable-portfolio');?>: </b>
												<br/>
												<img id="img_disp_img" name="img_disp_img"
													src="<?php echo $baseurl . $image_name; ?>" />
											</div>
                                                                                <?php }else{ ?>      
                                                                                            <img
												src="<?php echo plugins_url('/images/no-image-selected.png', __FILE__); ?>"
												id="img_disp_img" name="img_disp_img" />
                                                           
                                                                                     <?php } ?>
                                                                                         <img
												src="<?php echo plugins_url('/images/ajax-loader.gif', __FILE__); ?>"
												style="display: none" id="loading_img" name="loading_img" />
											<div style="clear: both"></div>
											<div></div>
											<div class="uploader">
												
												<div style="clear: both; margin-top: 15px;"></div>
                                                                                                        <a
													href="javascript:;" class="niks_media" id="myMediaUploader_image"><b><?php echo __('Click Here to upload Image','responsive-filterable-portfolio');?></b></a>
                                                                                                    <br /> <br />
												<div>
                                                                                                 <input id="HdnMediaSelection_image" name="HdnMediaSelection_image" type="hidden" value="<?php echo $HdnMediaSelection;?>" />
												</div>
												<div style="clear: both"></div>
												<div></div>
												<div style="clear: both"></div>

												<br />
											</div>
                                                                                </div>
                                                                            
                                                                            <script>
                                                                                 //uploading files variable
                                                                                  var custom_file_frame;
                                                                                  $n("#myMediaUploader_image").click(function(event) {
                                                                                    event.preventDefault();
                                                                                            //If the frame already exists, reopen it
                                                                                   if (typeof (custom_file_frame) !== "undefined") {
                                                                                    custom_file_frame.close();
                                                                                    }

                                                                                    //Create WP media frame.
                                                                                    custom_file_frame = wp.media.frames.customHeader = wp.media({
                                                                                    //Title of media manager frame
                                                                                    title: "WP Media Uploader",
                                                                                            library: {
                                                                                            type: 'image'
                                                                                            },
                                                                                            button: {
                                                                                            //Button text
                                                                                            text: "Set Image"
                                                                                            },
                                                                                            //Do not allow multiple files, if you want multiple, set true
                                                                                            multiple: false
                                                                                    });
                                                                                            //callback for selected image
                                                                                            custom_file_frame.on('select', function() {

                                                                                        var attachment = custom_file_frame.state().get('selection').first().toJSON();
                                                                                        var validExtensions = new Array();
                                                                                        validExtensions[0] = 'jpg';
                                                                                        validExtensions[1] = 'jpeg';
                                                                                        validExtensions[2] = 'png';
                                                                                        validExtensions[3] = 'gif';
                                                                                       
                                                                                        var inarr = parseInt($n.inArray(attachment.subtype, validExtensions));
                                                                                          if (inarr > 0 && attachment.type.toLowerCase() == 'image'){

                                                                                            var titleTouse = "";
                                                                                         
                                                                                             if ($n.trim(attachment.title) != ''){

                                                                                                 titleTouse = $n.trim(attachment.title);
                                                                                            }
                                                                                            else if ($n.trim(attachment.caption) != ''){

                                                                                                titleTouse = $n.trim(attachment.caption);
                                                                                            }

                                                                                           

                                                                                            $n("#addimage_ #title").val(titleTouse);
                                                                                           

                                                                                            if (attachment.id != ''){

                                                                                                      $n("#HdnMediaSelection_image").val(attachment.url);
                                                                                                      $n("#img_disp_img").attr('src', attachment.url);

                                                                                                }

                                                                                            }
                                                                                            else{

                                                                                              alert('Invalid image selection.');
                                                                                            }
                                                                                            //do something with attachment variable, for example attachment.filename
                                                                                            //Object:
                                                                                            //attachment.alt - image alt
                                                                                            //attachment.author - author id
                                                                                            //attachment.caption
                                                                                            //attachment.dateFormatted - date of image uploaded
                                                                                            //attachment.description
                                                                                            //attachment.editLink - edit link of media
                                                                                            //attachment.filename
                                                                                            //attachment.height
                                                                                            //attachment.icon - don't know WTF?))
                                                                                            //attachment.id - id of attachment
                                                                                            //attachment.link - public link of attachment, for example ""http://site.com/?attachment_id=115""
                                                                                            //attachment.menuOrder
                                                                                            //attachment.mime - mime type, for example image/jpeg"
                                                                                            //attachment.name - name of attachment file, for example "my-image"
                                                                                            //attachment.status - usual is "inherit"
                                                                                            //attachment.subtype - "jpeg" if is "jpg"
                                                                                            //attachment.title
                                                                                            //attachment.type - "image"
                                                                                            //attachment.uploadedTo
                                                                                            //attachment.url - http url of image, for example "http://site.com/wp-content/uploads/2012/12/my-image.jpg"
                                                                                            //attachment.width
                                                                                            });
                                                                                            //Open modal
                                                                                            custom_file_frame.open();
                                                                                      });
                                                                                    
                                                                                 </script>
                                                                        </div>
                                                                
                                                                        <div class="stuffbox" id="namediv" style="width: 100%">
										<h3>
											<label for="link_name"><?php echo __('Image Title','responsive-filterable-portfolio');?> (<span
												style="font-size: 11px; font-weight: normal"><?php echo __('Used into lightbox','responsive-filterable-portfolio');?></span>)
											</label>
										</h3>
										<div class="inside">
											<div>
												<input type="text" id="title" tabindex="1" size="30" name="title" value="<?php echo $videotitle; ?>">
											</div>
											<div style="clear: both"></div>
											<div></div>
											<div style="clear: both"></div>
										</div>
									</div>
									<div class="stuffbox" id="namediv" style="width: 100%">
										<h3>
											<label for="link_name"><?php echo __('Image Title Url','responsive-filterable-portfolio');?> (<span
												style="font-size: 11px; font-weight: normal"><?php echo __('click on title redirect to this url.Used in lightbox for image title','responsive-filterable-portfolio');?></span>)
											</label>
										</h3>
										<div class="inside">
											<div>
												<input type="text" id="murl" class="url"
													tabindex="1" size="30" name="murl"
													value="<?php echo $murl; ?>">
											</div>
											<div style="clear: both"></div>
											<div></div>
											<div style="clear: both"></div>

										</div>
									</div>
                                                                        <div class="stuffbox" id="namediv" style="width: 100%">
										<h3>
											<label for="mtag"><?php echo __('Media Tags','responsive-filterable-portfolio');?> (<span style="font-size: 11px; font-weight: normal"><?php echo __(' Tags used for filter on grid.You can add comma separated values.','responsive-filterable-portfolio'); ?></span>)
											</label>
										</h3>
										<div class="inside">
											<div>
												<input type="text" id="mtag" 
													tabindex="1" size="30" name="mtag"
													value="<?php echo $mtag; ?>">
											</div>
											<div style="clear: both"></div>
											<div></div>
											<div style="clear: both"></div>

										</div>
									</div>
                                                                        
									<div class="stuffbox" id="namediv" style="width: 100%">
										<h3>
											<label><?php echo __('Show Image In Lightbox?','responsive-filterable-portfolio');?> (<span
												style="font-size: 11px; font-weight: normal"><?php echo __('show image in lightbox or redirect ?','responsive-filterable-portfolio');?></span>)
											</label>
										</h3>
										<div class="inside">
											<table>
												<tr>
													<td>
														<div>
															<input type="checkbox" id="open_link_in" size="30"
																name="open_link_in" value=""
																<?php
																	if ($open_link_in == true) {
																		echo "checked='checked'";
																	}
																	?>
																style="width: 20px;">&nbsp;<?php echo __('Show image In Lightbox?','responsive-filterable-portfolio');?>
														</div>
														<div style="clear: both"></div>
														<div></div>
													</td>
												</tr>
											</table>
											<div style="clear: both"></div>
										</div>
									</div>
									
									

									
                                                                        <?php if (isset($_GET['id']) and $_GET['id'] > 0) { ?> 
										 <input type="hidden" name="imageid" id="imageid" value="<?php echo $_GET['id']; ?>">
                                                                         <?php
										}
										?>
                                                                            <?php wp_nonce_field('action_image_add_edit', 'add_edit_image_nonce'); ?>      
                                                                            <input type="submit"
										onclick="" name="btnsave" id="btnsave" value="<?php echo __('Save Changes','responsive-filterable-portfolio');?>"
										class="button-primary">&nbsp;&nbsp;<input type="button"
										name="cancle" id="cancle" value="<?php echo __('Cancel','responsive-filterable-portfolio');?>"
										class="button-primary"
										onclick="location.href = 'admin.php?page=responsive_portfolio_with_lightbox_media_management'">

								</form>
                                                                   <form method="post" action="" id="addlink_" name="addlink_" enctype="multipart/form-data" style="display:none">
                                                                    
                                                                        <input type="hidden" name="media_type" id="media_type" value="link" />
									 <div class="stuffbox" id="image_info" style="width: 100%;">
										<h3>
											<label for="link_name"><?php echo __('Image Information','responsive-filterable-portfolio');?></label>
										</h3>
										<div class="inside" id="fileuploaddiv">
                                                                                <?php if ($image_name != "") { ?>
                                                                                        <div>
												<b><?php echo __('Current Image','responsive-filterable-portfolio');?>: </b>
												<br/>
												<img id="img_disp_link" name="img_disp_link"
													src="<?php echo $baseurl . $image_name; ?>" />
											</div>
                                                                                <?php }else{ ?>      
                                                                                            <img
												src="<?php echo plugins_url('/images/no-image-selected.png', __FILE__); ?>"
												id="img_disp_link" name="img_disp_link" />
                                                           
                                                                                     <?php } ?>
                                                                                         <img
												src="<?php echo plugins_url('/images/ajax-loader.gif', __FILE__); ?>"
												style="display: none" id="loading_img" name="loading_img" />
											<div style="clear: both"></div>
											<div></div>
											<div class="uploader">
												
												<div style="clear: both; margin-top: 15px;"></div>
                                                                                                        <a
													href="javascript:;" class="niks_media" id="myMediaUploader_link"><b><?php echo __('Click Here to upload Image','responsive-filterable-portfolio');?></b></a>
                                                                                                    <br /> <br />
												<div>
                                                                                                 <input id="HdnMediaSelection_link" name="HdnMediaSelection_link" type="hidden" value="<?php echo $HdnMediaSelection;?>" />
												</div>
												<div style="clear: both"></div>
												<div></div>
												<div style="clear: both"></div>

												<br />
											</div>
                                                                                </div>
                                                                            
                                                                            <script>
                                                                                 //uploading files variable
                                                                                  var custom_file_frame;
                                                                                  $n("#myMediaUploader_link").click(function(event) {
                                                                                    event.preventDefault();
                                                                                            //If the frame already exists, reopen it
                                                                                   if (typeof (custom_file_frame) !== "undefined") {
                                                                                    custom_file_frame.close();
                                                                                    }

                                                                                    //Create WP media frame.
                                                                                    custom_file_frame = wp.media.frames.customHeader = wp.media({
                                                                                    //Title of media manager frame
                                                                                    title: "WP Media Uploader",
                                                                                            library: {
                                                                                            type: 'image'
                                                                                            },
                                                                                            button: {
                                                                                            //Button text
                                                                                            text: "Set Image"
                                                                                            },
                                                                                            //Do not allow multiple files, if you want multiple, set true
                                                                                            multiple: false
                                                                                    });
                                                                                            //callback for selected image
                                                                                            custom_file_frame.on('select', function() {

                                                                                        var attachment = custom_file_frame.state().get('selection').first().toJSON();
                                                                                        var validExtensions = new Array();
                                                                                        validExtensions[0] = 'jpg';
                                                                                        validExtensions[1] = 'jpeg';
                                                                                        validExtensions[2] = 'png';
                                                                                        validExtensions[3] = 'gif';
                                                                                       
                                                                                        var inarr = parseInt($n.inArray(attachment.subtype, validExtensions));
                                                                                          if (inarr > 0 && attachment.type.toLowerCase() == 'image'){

                                                                                            var titleTouse = "";
                                                                                            
                                                                                             if ($n.trim(attachment.title) != ''){

                                                                                                 titleTouse = $n.trim(attachment.title);
                                                                                            }
                                                                                            else if ($n.trim(attachment.caption) != ''){

                                                                                                titleTouse = $n.trim(attachment.caption);
                                                                                            }

                                                                                            

                                                                                            $n("#addlink_ #title").val(titleTouse);
                                                                                         
                                                                                            if (attachment.id != ''){

                                                                                                      $n("#HdnMediaSelection_link").val(attachment.url);
                                                                                                      $n("#img_disp_link").attr('src', attachment.url);

                                                                                                }

                                                                                            }
                                                                                            else{

                                                                                              alert('Invalid image selection.');
                                                                                            }
                                                                                            //do something with attachment variable, for example attachment.filename
                                                                                            //Object:
                                                                                            //attachment.alt - image alt
                                                                                            //attachment.author - author id
                                                                                            //attachment.caption
                                                                                            //attachment.dateFormatted - date of image uploaded
                                                                                            //attachment.description
                                                                                            //attachment.editLink - edit link of media
                                                                                            //attachment.filename
                                                                                            //attachment.height
                                                                                            //attachment.icon - don't know WTF?))
                                                                                            //attachment.id - id of attachment
                                                                                            //attachment.link - public link of attachment, for example ""http://site.com/?attachment_id=115""
                                                                                            //attachment.menuOrder
                                                                                            //attachment.mime - mime type, for example image/jpeg"
                                                                                            //attachment.name - name of attachment file, for example "my-image"
                                                                                            //attachment.status - usual is "inherit"
                                                                                            //attachment.subtype - "jpeg" if is "jpg"
                                                                                            //attachment.title
                                                                                            //attachment.type - "image"
                                                                                            //attachment.uploadedTo
                                                                                            //attachment.url - http url of image, for example "http://site.com/wp-content/uploads/2012/12/my-image.jpg"
                                                                                            //attachment.width
                                                                                            });
                                                                                            //Open modal
                                                                                            custom_file_frame.open();
                                                                                      });
                                                                                    
                                                                                 </script>
                                                                        </div>
                                                                
                                                                        <div class="stuffbox" id="namediv" style="width: 100%">
										<h3>
											<label for="link_name"><?php echo __('Link Title','responsive-filterable-portfolio');?> (<span
												style="font-size: 11px; font-weight: normal"><?php echo __('Used into Caption','responsive-filterable-portfolio'); ?></span>)
											</label>
										</h3>
										<div class="inside">
											<div>
												<input type="text" id="title" tabindex="1" size="30" name="title" value="<?php echo $videotitle; ?>">
											</div>
											<div style="clear: both"></div>
											<div></div>
											<div style="clear: both"></div>
										</div>
									</div>
									<div class="stuffbox" id="namediv" style="width: 100%">
										<h3>
											<label for="link_name"><?php echo __('Link Url','responsive-filterable-portfolio');?> (<span
												style="font-size: 11px; font-weight: normal"><?php echo __('click on image will redirect to this url.','responsive-filterable-portfolio'); ?></span>)
											</label>
										</h3>
										<div class="inside">
											<div>
												<input type="text" id="murl" class="url"
													tabindex="1" size="30" name="murl"
													value="<?php echo $murl; ?>">
											</div>
											<div style="clear: both"></div>
											<div></div>
											<div style="clear: both"></div>

										</div>
									</div>
									<div class="stuffbox" id="namediv" style="width: 100%">
										<h3>
											<label for="mtag"><?php echo __('Media Tags','responsive-filterable-portfolio');?> (<span
												style="font-size: 11px; font-weight: normal"><?php echo __(' Tags used for filter on grid.You can add comma separated values.','responsive-filterable-portfolio'); ?></span>)
											</label>
										</h3>
										<div class="inside">
											<div>
												<input type="text" id="mtag" 
													tabindex="1" size="30" name="mtag"
													value="<?php echo $mtag; ?>">
											</div>
											<div style="clear: both"></div>
											<div></div>
											<div style="clear: both"></div>

										</div>
									</div>
                                                                    
									
                                                                        <?php if (isset($_GET['id']) and (int) $_GET['id'] > 0) { ?> 
										 <input type="hidden" name="linkid" id="linkid" value="<?php echo (int) $_GET['id']; ?>">
                                                                         <?php
										}
										?>
                                                                            <?php wp_nonce_field('action_image_add_edit', 'add_edit_image_nonce'); ?>      
                                                                            <input type="submit"
										onclick="" name="btnsave" id="btnsave" value="<?php echo __('Save Changes','responsive-filterable-portfolio');?>"
										class="button-primary">&nbsp;&nbsp;<input type="button"
										name="cancle" id="cancle" value="<?php echo __('Cancel','responsive-filterable-portfolio');?>"
										class="button-primary"
										onclick="location.href = 'admin.php?page=responsive_portfolio_with_lightbox_media_management'">

								</form>
								<script type="text/javascript">

                                                                    var $n = jQuery.noConflict();
                                                                    $n(document).ready(function() {

                                                                     $n.validator.setDefaults({ 
                                                                         ignore: [],
                                                                         // any other default options and/or rules
                                                                     });

                                                                     $n("#addvideo").validate({
                                                                     rules: {
                                                                     videotitle: {
                                                                     required:true,
                                                                             maxlength: 200
                                                                     },
                                                                             vtype: {
                                                                             required:true

                                                                             },
                                                                             videourl: {
                                                                             required:true,
                                                                                     url:true,
                                                                                     maxlength: 500
                                                                             },
                                                                             HdnMediaSelection:{
                                                                               required:true  
                                                                             },
                                                                             videotitleurl: {

                                                                             url:true,
                                                                              maxlength: 500
                                                                             },
                                                                             mtag:{
                                                                             maxlength: 5000
                                                                             }
                                                                            },
                                                                             errorClass: "image_error",
                                                                             errorPlacement: function(error, element) {
                                                                             error.appendTo(element.parent().next().next());
                                                                             }, messages: {
                                                                                 HdnMediaSelection: "Please select video thumbnail or Upload by wordpress media uploader.",

                                                                             }

                                                                         })
                                                                         
                                                                         
                                                                           $n("#addimage_").validate({
                                                                            rules: {
                                                                             HdnMediaSelection_image:{
                                                                               required:true  
                                                                             },
                                                                             murl: {

                                                                             url:true,
                                                                              maxlength: 500
                                                                             },
                                                                             mtag:{
                                                                             maxlength: 5000
                                                                             }
                                                                            },
                                                                             errorClass: "image_error",
                                                                             errorPlacement: function(error, element) {
                                                                             error.appendTo(element.parent().next().next());
                                                                             }, messages: {
                                                                                 HdnMediaSelection: "Please select image thumbnail or Upload by wordpress media uploader.",

                                                                             }

                                                                         })
                                                                           $n("#addlink_").validate({
                                                                            rules: {
                                                                             HdnMediaSelection_link:{
                                                                               required:true  
                                                                             },
                                                                             murl: {
                                                                                required:true,      
                                                                                url:true,
                                                                                maxlength: 500
                                                                             },
                                                                              mtag:{
                                                                             maxlength: 5000
                                                                             }
                                                                            },
                                                                             errorClass: "image_error",
                                                                             errorPlacement: function(error, element) {
                                                                             error.appendTo(element.parent().next().next());
                                                                             }, messages: {
                                                                                 HdnMediaSelection: "Please select link thumbnail or Upload by wordpress media uploader.",

                                                                             }

                                                                         
                                                                         
                                                                       })
                                                                           
                                                                         
                                                                     });
                                                                     
                                                                   
                                                                 </script>

							</div>
						</div>
					</div>
				</div>
                   </div>
                   <div id="postbox-container-1" class="postbox-container" > 

                        <div class="postbox"> 
                            <h3 class="hndle"><span></span><?php echo __('Access All Themes In One Price','responsive-filterable-portfolio');?></h3> 
                            <div class="inside">
                                <center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank">
                                        <img border="0" src="<?php echo plugins_url( 'images/300x250.gif', __FILE__ );?>" width="250" height="250">
                                    </a></center>

                                <div style="margin:10px 5px">

                                </div>
                            </div></div>
                        <div class="postbox"> 
                            <h3 class="hndle"><span></span><?php echo __('Recommended WordPress Hostings','responsive-filterable-portfolio');?></h3> 
                            <div class="inside">
                                <center><a href="https://mythemeshop.com/?ref=nik_gandhi007" target="_blank">
                                        <img src="<?php echo plugins_url( 'images/300x250.png', __FILE__ );?>" width="250" height="250" border="0">
                                    </a></center>
                                <div style="margin:10px 5px">
                                </div>
                            </div></div>

                    </div>   
                  </div>
          </div>    
<?php
		}
	} else if (strtolower ( $action ) == strtolower ( 'delete' )) {
		
             $retrieved_nonce = '';

              if(isset($_GET['nonce']) and $_GET['nonce']!=''){

                  $retrieved_nonce=$_GET['nonce'];

              }
              if (!wp_verify_nonce($retrieved_nonce, 'delete_image' ) ){


                  wp_die('Security check fail'); 
              }

		$uploads = wp_upload_dir ();
		$baseDir = $uploads ['basedir'];
		$baseDir = str_replace ( "\\", "/", $baseDir );
		$pathToImagesFolder = $baseDir . '/wp-best-portfolio';
		
		
		
		$location = "admin.php?page=responsive_portfolio_with_lightbox_media_management";
		$deleteId = (int) htmlentities(strip_tags($_GET ['id']),ENT_QUOTES);
		
		try {
			
			$query = "SELECT * FROM " . $wpdb->prefix . "e_portfolio WHERE id=$deleteId";
			$myrow = $wpdb->get_row ( $query );
			
                        $settings = get_option( 'best_portfolio_grid_settings' );
                        $imageheight = $settings ['imageheight'];
                        $imagewidth = $settings ['imagewidth'];

			if (is_object ( $myrow )) {
				
				$image_name = $myrow->image_name;
				$wpcurrentdir = dirname ( __FILE__ );
				$wpcurrentdir = str_replace ( "\\", "/", $wpcurrentdir );
				$imagetoDel = $pathToImagesFolder . '/' . $image_name;
                                $pInfo = pathinfo ( $myrow->HdnMediaSelection );
                                $ext = $pInfo ['extension'];
							
				@unlink ( $imagetoDel );
                                @unlink($pathToImagesFolder.'/'.$myrow->vid . '_big_'.$imageheight.'_'.$imagewidth.'.'.$ext);
				
				$query = "delete from  " . $wpdb->prefix . "e_portfolio where id=$deleteId";
				$wpdb->query ( $query );
				
				$wp_best_portfolio_msg = array ();
				$wp_best_portfolio_msg ['type'] = 'succ';
				$wp_best_portfolio_msg ['message'] = __('Video deleted successfully.','responsive-filterable-portfolio');
				update_option ( 'wp_best_portfolio_msg', $wp_best_portfolio_msg );
			}
		} catch ( Exception $e ) {
			
			$wp_best_portfolio_msg = array ();
			$wp_best_portfolio_msg ['type'] = 'err';
			$wp_best_portfolio_msg ['message'] = __('Error while deleting video.','responsive-filterable-portfolio');
			update_option ( 'wp_best_portfolio_msg', $wp_best_portfolio_msg );
		}
		
		echo "<script type='text/javascript'> location.href='$location';</script>";
		exit ();
	} else if (strtolower ( $action ) == strtolower ( 'deleteselected' )) {
		
                if(!check_admin_referer('action_settings_mass_delete','mass_delete_nonce')){

                        wp_die('Security check fail'); 
                  }

		
		
		$location = "admin.php?page=responsive_portfolio_with_lightbox_media_management";
		
		if (isset ( $_POST ) and isset ( $_POST ['deleteselected'] ) and ($_POST ['action'] == 'delete' or $_POST ['action_upper'] == 'delete')) {
			
			$uploads = wp_upload_dir ();
			$baseDir = $uploads ['basedir'];
			$baseDir = str_replace ( "\\", "/", $baseDir );
			$pathToImagesFolder = $baseDir . '/wp-best-portfolio';
			
			if (sizeof ( $_POST ['thumbnails'] ) > 0) {
				
				$deleteto = $_POST ['thumbnails'];
				$implode = implode ( ',', $deleteto );
				
				try {
					
					foreach ( $deleteto as $img ) {
						
						$query = "SELECT * FROM " . $wpdb->prefix . "e_portfolio WHERE id=$img";
						$myrow = $wpdb->get_row ( $query );
                                                
                                                $settings = get_option( 'best_portfolio_grid_settings' );
                                                $imageheight = $settings ['imageheight'];
                                                $imagewidth = $settings ['imagewidth'];

						
						if (is_object ( $myrow )) {
							
							$image_name = $myrow->image_name ;
							$wpcurrentdir = dirname ( __FILE__ );
							$wpcurrentdir = str_replace ( "\\", "/", $wpcurrentdir );
							$imagetoDel = $pathToImagesFolder . '/' . $image_name;
							
                                                        $pInfo = pathinfo ( $myrow->HdnMediaSelection );
                                                        $ext = $pInfo ['extension'];
							
                                                        @unlink ( $imagetoDel );
                                                        @unlink($pathToImagesFolder.'/'.$myrow->vid . '_big_'.$imageheight.'_'.$imagewidth.'.'.$ext);
				
							
							$query = "delete from  " . $wpdb->prefix . "e_portfolio where id=$img";
							$wpdb->query ( $query );
							
							$wp_best_portfolio_msg = array ();
							$wp_best_portfolio_msg ['type'] = 'succ';
							$wp_best_portfolio_msg ['message'] = __('Selected videos deleted successfully.','responsive-filterable-portfolio');
							update_option ( 'wp_best_portfolio_msg', $wp_best_portfolio_msg );
						}
					}
				} catch ( Exception $e ) {
					
					$wp_best_portfolio_msg = array ();
					$wp_best_portfolio_msg ['type'] = 'err';
					$wp_best_portfolio_msg ['message'] = __('Error while deleting videos.','responsive-filterable-portfolio');
					update_option ( 'wp_best_portfolio_msg', $wp_best_portfolio_msg );
				}
				
				echo "<script type='text/javascript'> location.href='$location';</script>";
				exit ();
			} else {
				
				echo "<script type='text/javascript'> location.href='$location';</script>";
				exit ();
			}
		} else {
			
			echo "<script type='text/javascript'> location.href='$location';</script>";
			exit ();
		}
	}
}
function rfp_responsive_portfolio_with_lightbox_media_preview_func() {
    
	global $wpdb;
        
        $pagenum = isset($_GET['pagenum']) ? (int) absint($_GET['pagenum']) : 1;
         
	$settings = get_option( 'best_portfolio_grid_settings' );
	 
	$rand_Numb = uniqid ( 'thumnail_slider' );
	$rand_Num_td = uniqid ( 'divSliderMain' );
	$rand_var_name = uniqid ( 'rand_' );
	
	$location = "admin.php?page=responsive_portfolio_with_lightbox_media_preview";
	
	$wpcurrentdir = dirname ( __FILE__ );
	$wpcurrentdir = str_replace ( "\\", "/", $wpcurrentdir );
	// $settings=get_option('thumbnail_slider_settings');
	
	$uploads = wp_upload_dir ();
	$baseDir = $uploads ['basedir'];
	$baseDir = str_replace ( "\\", "/", $baseDir );
	$pathToImagesFolder = $baseDir . '/wp-best-portfolio';
	$baseurl = $uploads ['baseurl'];
	$baseurl .= '/wp-best-portfolio/';
	
	$wpcurrentdir = dirname ( __FILE__ );
	$wpcurrentdir = str_replace ( "\\", "/", $wpcurrentdir );
	$randOmeAlbName = uniqid ( 'alb_' );
	$randOmeRel = uniqid ( 'rel_' );
        $randOmVlBox = uniqid('video_lbox_');
        $vNonce = wp_create_nonce('vNonce');
        $url = plugin_dir_url(__FILE__);
        $loaderImg = $url . 'images/bx_loader.gif';
        
        $LoadingBackColor=$settings ['BackgroundColor'];
        if(strtolower($LoadingBackColor)=='none'){
            $LoadingBackColor='#ffffff';
        }
        
	?>
                <div id="poststuff" > 
                <div id="post-body" class="metabox-holder columns-2" >  
                   
                            <div class="post-body-content">
                                     <span><h3 style="color: blue;"><a target="_blank" href="http://www.i13websolution.com/wordpress-responsive-media-portfolio-grid.html"><?php echo __('UPGRADE TO PRO VERSION','responsive-filterable-portfolio');?></a></h3></span>
                                    <h2><?php echo __('Portfolio Preview','responsive-filterable-portfolio');?></h2>
                                    <br />
                            <?php if (is_array($settings)) { ?>
                                <div id="poststuff">
				 <div id="post-body" class="metabox-holder ">
				   <div id="post-body-content">
					<div style="clear: both;"></div>
                                            <?php $url = plugin_dir_url(__FILE__); ?>           

                                            <div class="container_">
                                                
                                                <div class="gallery_" id="<?php echo $rand_var_name;?>" style="visibility:hidden">
                                                    <div id="<?php echo $rand_var_name; ?>_overlay_grid" class="overlay_grid" style="background: <?php echo $LoadingBackColor; ?> url('<?php echo $loaderImg; ?>') no-repeat scroll 50% 50%;" ></div>
                                                     <div id="<?php echo $rand_Num_td; ?>">
                                                         <?php if($settings['show_filters']):?>
                                                            
                                                            <div id="FilerTab" class="fil">
                                                                        <div class="shield_cli" id="shield_<?php echo $rand_var_name;?>" ></div>
                                                                        <?php
                                                                           $query = 'SET SESSION group_concat_max_len=150000';
                                                                           $wpdb->query($query);
                                                                           
                                                                           $filerValsQuery="select GROUP_CONCAT(mtag) as filters from 
                                                                                            (SELECT mtag from ".$wpdb->prefix."e_portfolio  order by createdon,id desc  ) a";
                                                                          
                                                                           $filters=$wpdb->get_var($filerValsQuery);
                                                                           $filtersArr=  explode(",",$filters);
                                                                           $filtersArr=array_unique($filtersArr);
                                                                           sort($filtersArr);
                                                                         
                                                                           
                                                                        ?>
                                                                        <a href="#" class="sortLink selected" data-category="all"><?php echo $settings['AllKeywordTranslate'];?></a>
                                                                        <?php foreach($filtersArr as $val):?>
                                                                            <?php if(trim($val)!=''): ?>
                                                                              <a href="#" class="sortLink" data-category="<?php echo $val;?>"><?php echo $val;?></a>
                                                                            <?php endif;?>  
                                                                        <?php endforeach;?>
                                                                        <div class="clear_floats"></div>
                                                                </div>
                                                         <?php endif;?>
                                                                 <div class="photos">
				
                                                                    <div class="thumbnail_wrap_">

                                                                      <?php
                                                                        
                                                                        $imageheight = $settings ['imageheight'];
                                                                        $imagewidth = $settings ['imagewidth'];
                                                                        $query = "SELECT * FROM " . $wpdb->prefix . "e_portfolio   order by createdon,id desc";
                                                                        $firstChild='firstimg';
                                                                        $rows = $wpdb->get_results ( $query, 'ARRAY_A' );
                                                                        
                                                                        
                                                                        if (count ( $rows ) > 0) {

                                                                            foreach ( $rows as $row ) {
                                                                                
                                                                                    $imagename = $row ['image_name'];
                                                                                    $video_url = $row ['videourl'];
                                                                                    $imageUploadTo = $pathToImagesFolder . '/' . $imagename;
                                                                                    $imageUploadTo = str_replace ( "\\", "/", $imageUploadTo );
                                                                                    $pathinfo = pathinfo ( $imageUploadTo );
                                                                                    $filenamewithoutextension = $pathinfo ['filename'];
                                                                                    $outputimg = "";

                                                                                    $outputimgmain = $baseurl . $row ['image_name'];
                                                                                  
                                                                                    $imagetoCheck = $pathToImagesFolder . '/' . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo ['extension'];
                                                                                 
                                                                                    if (file_exists ( $imagetoCheck )) {
                                                                                            $outputimg = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo ['extension'];
                                                                                    } else {

                                                                                            if (function_exists ( 'wp_get_image_editor' )) {

                                                                                                    $image = wp_get_image_editor ( $pathToImagesFolder . "/" . $row ['image_name'] );

                                                                                                    if (! is_wp_error ( $image )) {
                                                                                                            $image->resize ( $imagewidth, $imageheight, true );
                                                                                                            $image->save ( $imagetoCheck );
                                                                                                            $outputimg = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo ['extension'];
                                                                                                    } else {
                                                                                                            $outputimg = $baseurl . $row ['image_name'];
                                                                                                    }
                                                                                            } else if (function_exists ( 'image_resize' )) {

                                                                                                    $return = image_resize ( $pathToImagesFolder . "/" . $row ['image_name'], $imagewidth, $imageheight );
                                                                                                    if (! is_wp_error ( $return )) {

                                                                                                            $isrenamed = rename ( $return, $imagetoCheck );
                                                                                                            if ($isrenamed) {
                                                                                                                    $outputimg = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo ['extension'];
                                                                                                            } else {
                                                                                                                    $outputimg = $baseurl . $row ['image_name'];
                                                                                                            }
                                                                                                    } else {
                                                                                                            $outputimg = $baseurl . $row ['image_name'];
                                                                                                    }
                                                                                            } else {

                                                                                                    $outputimg = $baseurl . $row ['image_name'];
                                                                                            }

                                                                                            // $url = plugin_dir_url(__FILE__)."imagestoscroll/".$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];
                                                                                    }
                                                                                   
                                                                            $media_type=$row['media_type'];  
                                                                            $hoverClass='';
                                                                            if($media_type=="link")
                                                                                 $hoverClass="playbtnCss_link";
                                                                            else if($media_type=="video")
                                                                                 $hoverClass="playbtnCss_video";
                                                                             else if($media_type=="image")
                                                                                  $hoverClass="playbtnCss_zoom";
                                                                             
                                                                                $title = "";
                                                                                $rowTitle = $row['title'];
                                                                                $rowTitle = str_replace("'", "’", $rowTitle);
                                                                                $rowTitle = str_replace('"', '”', $rowTitle);

                                                                               
                                                                           
                                                                                $open_link_in = $row['open_link_in'];
                                                                                $openImageInNewTab = '_self';
                                                                               $embed_url=$row['embed_url'];
                                                                               if($media_type=="video"){
                                                                                  
                                                                                    if (trim($row['title']) != '' and trim($row['videourl']) != '') {

                                                                                        $title = "<a class='Imglink' target='$openImageInNewTab' href='{$row['murl']}'>{$rowTitle}</a>";
                                                                                        
                                                                                    } else if (trim($row['title']) != '' and trim($row['videourl']) == '') {

                                                                                        $title = "<a class='Imglink' >{$rowTitle}</a>";
                                                                                        
                                                                                    } 
                                                                               }
                                                                               else if($media_type=="image"){
                                                                                   
                                                                                   if (trim($row['title']) != '' and trim($row['murl']) != '') {

                                                                                        $title = "<a class='Imglink' target='$openImageInNewTab' href='{$row['murl']}'>{$rowTitle}</a>";
                                                                                        
                                                                                    } else if (trim($row['title']) != '' and trim($row['murl']) == '') {

                                                                                        $title = "<a class='Imglink' >{$rowTitle}</a>";
                                                                                        
                                                                                    }
                                                                                   
                                                                               }
                                                                               
                                                                            ?>

                                                                                <?php if($media_type=='image' or $media_type=='video') :?>

                                                                                        <?php if ($open_link_in == 1): ?>
                                                                                            <a data-rel="<?php echo $randOmeRel; ?>"  data-type="<?php echo $media_type;?>" data-overlay="1" data-title="<?php echo $title; ?>" class="thumbnail_ <?php echo $randOmVlBox; ?> <?php if($media_type=='video'):?>   iframe <?php endif;?>" data-categories="<?php echo $row['mtag'];?>" href="<?php if($media_type=='video'):?><?php echo $embed_url; ?> <?php else:?><?php echo $outputimgmain; ?><?php endif;?>" >
                                                                                                <div class="thum_div figure">

                                                                                                    <img src="<?php echo $outputimg;?>" alt="<?php echo $rowTitle; ?>"  title="<?php echo $rowTitle;?>" />
                                                                                                     <div class="<?php echo $hoverClass;?>"></div>

                                                                                                 </div>  
                                                                                            </a>
                                                                                         <?php else: ?>
                                                                        
                                                                                             <a   data-type="<?php echo $media_type;?>" data-title="<?php echo $title; ?>" class="thumbnail_ " data-categories="<?php echo $row['mtag'];?>" href="<?php if($media_type=='video'):?><?php echo $embed_url; ?> <?php else:?><?php echo $outputimgmain; ?><?php endif;?>" >
                                                                                                <div class="thum_div figure">

                                                                                                    <img src="<?php echo $outputimg;?>" alt="<?php echo $rowTitle; ?>"  title="<?php echo $rowTitle;?>" />
                                                                                                    
                                                                                                     <div class="<?php echo $hoverClass;?>"></div>

                                                                                                 </div>  
                                                                                            </a>
                                                                                         <?php endif;?>

                                                                                 <?php else:?>
                                                                                     <a   data-type="<?php echo $media_type;?>" class="thumbnail_ " data-categories="<?php echo $row['mtag'];?>" href="<?php echo $row['murl']; ?>" >
                                                                                        <div class="thum_div figure">

                                                                                            <img src="<?php echo $outputimg;?>" alt="<?php echo $rowTitle; ?>"  title="<?php echo $rowTitle;?>" />
                                                                                             <div class="<?php echo $hoverClass;?>"></div>

                                                                                         </div>  
                                                                                    </a>

                                                                                 <?php endif;?>
                                                                          
                                                                                <?php } ?>   
                                                                        
                                                                     <?php } ?>   

                                                                    </div><!-- .thumbnail_wrap end -->
                                                                     
                                                            </div>   
                                                            
                                                        </div>
                                                     <script>
                                                        var $n = jQuery.noConflict();
                                                        <?php $uniqId = uniqid(); ?>
                                                         var uniqObj<?php echo $uniqId ?> = $n("a[rel='<?php echo $randOmeRel; ?>']");

                                                        $n(document).ready(function() {



                                                                var func=$n('#<?php echo $rand_Num_td;?>').filterMediank({

                                                                    thumbWidth: <?php echo $settings['imagewidth'];?>,
                                                                    thumbHeight: <?php echo $settings['imageheight'];?>,
                                                                    thumbsSpacing:<?php echo $settings['imageMargin'];?>,
                                                                    galleryId:"<?php echo $rand_var_name;?>",
                                                                    backgroundColor:"<?php echo $settings['BackgroundColor'];?>"
                                                                });
                                                                $n('#<?php echo $rand_var_name;?>').css('visibility','visible')
                                                                
                                                     var globalTimer = null;

                                                        $n(window).resize(function() {
                                                            clearTimeout(globalTimer);
                                                            globalTimer = setTimeout(doneResize, 500);
                                                        });

                                                    function doneResize(){

                                                      func.resizeWin('<?php echo $rand_var_name;?>');
                                                    }

                                                     $n(".<?php echo $randOmVlBox; ?>").fancybox_fp({

                                                            'overlayColor':'#000000',
                                                            'padding': 3,
                                                            'margin': 20,
                                                            'autoScale': true,
                                                            'autoDimensions':true,
                                                            'uniqObj':uniqObj<?php echo $uniqId; ?>,
                                                            'uniqRel':'<?php echo $randOmeRel; ?>',
                                                            'transitionIn':'fade',
                                                            'transitionOut':'fade',
                                                            'titlePosition': 'outside',
                                                             'cyclic':true,
                                                            'hideOnContentClick':false,
                                                            'width' : 650,
                                                            'height' : 400,
                                                             'titleFormat': function(title, currentArray, currentIndex, currentOpts) {

                                                                    var currtElem = $n('#<?php echo $rand_Num_td; ?> a[href="' + currentOpts.href + '"]');
                                                                            var isoverlay = $n(currtElem).attr('data-overlay')

                                                                     if (isoverlay == "1" && $n.trim(title) != ""){
                                                                            return '<span id="fancybox_fp-title-over">' + title + '</span>';
                                                                    }
                                                                    else{
                                                                         return '';
                                                                    }

                                                                    }
                                                            });
                                                            

                                                    });  
                                                  </script>
                                                </div>
                                                
                                            </div>   
                                           
                                          
					</div>
				   </div>
				</div>  
                                <div id="postbox-container-1" class="postbox-container" > 

                                <div class="postbox"> 
                                    <h3 class="hndle"><span></span><?php echo __('Speed Test For Your WP','responsive-filterable-portfolio');?></h3> 
                                    <div class="inside">
                                        <center><a href="http://shareasale.com/r.cfm?b=875645&amp;u=675922&amp;m=41388&amp;urllink=&amp;afftrack=" target="_blank">
                                                <img src="<?php echo plugins_url( 'images/300x250.png', __FILE__ );?>" width="250" height="250" border="0">
                                            </a></center>
                                        <div style="margin:10px 5px">
                                        </div>
                                    </div></div>
                                <div class="postbox"> 
                                    <h3 class="hndle"><span></span><?php echo __('Recommended WordPress Plugin','responsive-filterable-portfolio');?></h3> 
                                    <div class="inside">
                                        <center><a href="http://shareasale.com/r.cfm?b=838943&amp;u=675922&amp;m=64312&amp;urllink=&amp;afftrack=" target="_blank">
                                                <img src="<?php echo plugins_url( 'images/wpforms-336x280v1.png', __FILE__ );?>" width="250" height="250" border="0">
                                            </a></center>
                                        <div style="margin:10px 5px">
                                        </div>
                                    </div></div>

                            </div>       
                        </div>
			</div>
			<div class="clear"></div>
			</div>
                <?php if (is_array($settings)) { ?>

                    <h3><?php echo __('To print this video gallery into WordPress Post/Page use below code','responsive-filterable-portfolio');?></h3>
                            <input type="text" value='[print_responsive_portfolio_plus_lightbox] '
                                    style="width: 400px; height: 30px"
                                    onclick="this.focus(); this.select()" />
                            <div class="clear"></div>
                            <h3><?php echo __('To print this video gallery into WordPress theme/template PHP files use below code','responsive-filterable-portfolio');?></h3>
                    <?php
			$shortcode = '[print_responsive_portfolio_plus_lightbox]';
		    ?>
                    <input type="text" value="&lt;?php echo do_shortcode('<?php echo htmlentities($shortcode, ENT_QUOTES); ?>'); ?&gt;" style="width: 400px; height: 30px" onclick="this.focus(); this.select()" />
                <?php } ?>
                <div class="clear"></div>
 <?php
   }
}
function rfp_print_responsive_portfolio_plus_lightbox_func($atts) {
    
    
	global $wpdb;
	
        ob_start();
        global $wpdb;
        
        
	$settings = get_option( 'best_portfolio_grid_settings' );
	
	$rand_Numb = uniqid ( 'thumnail_slider' );
	$rand_Num_td = uniqid ( 'divSliderMain' );
	$rand_var_name = uniqid ( 'rand_' );
	
	
	$wpcurrentdir = dirname ( __FILE__ );
	$wpcurrentdir = str_replace ( "\\", "/", $wpcurrentdir );
	// $settings=get_option('thumbnail_slider_settings');
	
	$uploads = wp_upload_dir ();
	$baseDir = $uploads ['basedir'];
	$baseDir = str_replace ( "\\", "/", $baseDir );
	$pathToImagesFolder = $baseDir . '/wp-best-portfolio';
	$baseurl = $uploads ['baseurl'];
	$baseurl .= '/wp-best-portfolio/';
	
	$wpcurrentdir = dirname ( __FILE__ );
	$wpcurrentdir = str_replace ( "\\", "/", $wpcurrentdir );
	$randOmeAlbName = uniqid ( 'alb_' );
	$randOmeRel = uniqid ( 'rel_' );
        $randOmVlBox = uniqid('video_lbox_');
        $vNonce = wp_create_nonce('vNonce');
        $url = plugin_dir_url(__FILE__);
        $loaderImg = $url . 'images/bx_loader.gif';
        
        $LoadingBackColor=$settings ['BackgroundColor'];
        if(strtolower($LoadingBackColor)=='none'){
            $LoadingBackColor='#ffffff';
        }
        if (is_array($settings)) { ?>
            
             
                    <div style="clear: both;"></div>
                        <?php $url = plugin_dir_url(__FILE__); ?>           

                        <div class="container_">

                            <div class="gallery_" id="<?php echo $rand_var_name;?>" style="visibility:hidden">
                                <div id="<?php echo $rand_var_name; ?>_overlay_grid" class="overlay_grid" style="background: <?php echo $LoadingBackColor; ?> url('<?php echo $loaderImg; ?>') no-repeat scroll 50% 50%;" ></div>
                                 <div id="<?php echo $rand_Num_td; ?>">
                                     <?php if($settings['show_filters']):?>
                                    
                                        <div id="FilerTab" class="fil">
                                                 <div class="shield_cli" id="shield_<?php echo $rand_var_name;?>" ></div>
                                                    <?php
                                                       $query = 'SET SESSION group_concat_max_len=150000';
                                                       $wpdb->query($query);

                                                       $filerValsQuery="select GROUP_CONCAT(mtag) as filters from 
                                                                        (SELECT mtag from ".$wpdb->prefix."e_portfolio  order by createdon,id desc ) a";

                                                       $filters=$wpdb->get_var($filerValsQuery);
                                                       $filtersArr=  explode(",",$filters);
                                                       $filtersArr=array_unique($filtersArr);
                                                       sort($filtersArr);


                                                    ?>
                                                    <a href="#" class="sortLink selected" data-category="all"><?php echo $settings['AllKeywordTranslate'];?></a>
                                                    <?php foreach($filtersArr as $val):?>
                                                         <?php if(trim($val)!=''): ?>    
                                                           <a href="#" class="sortLink" data-category="<?php echo $val;?>"><?php echo $val;?></a>
                                                         <?php endif;?> 
                                                    <?php endforeach;?>
                                                    <div class="clear_floats"></div>
                                            </div>
                                     <?php endif;?>
                                             <div class="photos">

                                                <div class="thumbnail_wrap_">

                                                  <?php

                                                    $imageheight = $settings ['imageheight'];
                                                    $imagewidth = $settings ['imagewidth'];
                                                    $query = "SELECT * FROM " . $wpdb->prefix . "e_portfolio  order by createdon,id desc ";
                                                    $firstChild='firstimg';
                                                    $rows = $wpdb->get_results ( $query, 'ARRAY_A' );


                                                    if (count ( $rows ) > 0) {

                                                        foreach ( $rows as $row ) {

                                                                $imagename = $row ['image_name'];
                                                                $video_url = $row ['videourl'];
                                                                $imageUploadTo = $pathToImagesFolder . '/' . $imagename;
                                                                $imageUploadTo = str_replace ( "\\", "/", $imageUploadTo );
                                                                $pathinfo = pathinfo ( $imageUploadTo );
                                                                $filenamewithoutextension = $pathinfo ['filename'];
                                                                $outputimg = "";

                                                                $outputimgmain = $baseurl . $row ['image_name'];

                                                                $imagetoCheck = $pathToImagesFolder . '/' . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo ['extension'];

                                                                if (file_exists ( $imagetoCheck )) {
                                                                        $outputimg = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo ['extension'];
                                                                } else {

                                                                        if (function_exists ( 'wp_get_image_editor' )) {

                                                                                $image = wp_get_image_editor ( $pathToImagesFolder . "/" . $row ['image_name'] );

                                                                                if (! is_wp_error ( $image )) {
                                                                                        $image->resize ( $imagewidth, $imageheight, true );
                                                                                        $image->save ( $imagetoCheck );
                                                                                        $outputimg = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo ['extension'];
                                                                                } else {
                                                                                        $outputimg = $baseurl . $row ['image_name'];
                                                                                }
                                                                        } else if (function_exists ( 'image_resize' )) {

                                                                                $return = image_resize ( $pathToImagesFolder . "/" . $row ['image_name'], $imagewidth, $imageheight );
                                                                                if (! is_wp_error ( $return )) {

                                                                                        $isrenamed = rename ( $return, $imagetoCheck );
                                                                                        if ($isrenamed) {
                                                                                                $outputimg = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo ['extension'];
                                                                                        } else {
                                                                                                $outputimg = $baseurl . $row ['image_name'];
                                                                                        }
                                                                                } else {
                                                                                        $outputimg = $baseurl . $row ['image_name'];
                                                                                }
                                                                        } else {

                                                                                $outputimg = $baseurl . $row ['image_name'];
                                                                        }

                                                                        // $url = plugin_dir_url(__FILE__)."imagestoscroll/".$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];
                                                                }

                                                        $media_type=$row['media_type'];  
                                                        $hoverClass='';
                                                        if($media_type=="link")
                                                             $hoverClass="playbtnCss_link";
                                                        else if($media_type=="video")
                                                             $hoverClass="playbtnCss_video";
                                                         else if($media_type=="image")
                                                              $hoverClass="playbtnCss_zoom";

                                                            $title = "";
                                                            $rowTitle = $row['title'];
                                                            $rowTitle = str_replace("'", "’", $rowTitle);
                                                            $rowTitle = str_replace('"', '”', $rowTitle);

                                                           

                                                            $open_link_in = $row['open_link_in'];
                                                            $openImageInNewTab = '_self';
                                                            $embed_url=$row['embed_url'];
                                                           if($media_type=="video"){

                                                                if (trim($row['title']) != '' and trim($row['videourl']) != '') {

                                                                    $title = "<a class='Imglink' target='$openImageInNewTab' href='{$row['murl']}'>{$rowTitle}</a>";
                                                                   
                                                                } else if (trim($row['title']) != '' and trim($row['videourl']) == '') {

                                                                    $title = "<a class='Imglink' >{$rowTitle}</a>";
                                                                   
                                                                } 
                                                           }
                                                           else if($media_type=="image"){

                                                               if (trim($row['title']) != '' and trim($row['murl']) != '') {

                                                                    $title = "<a class='Imglink' target='$openImageInNewTab' href='{$row['murl']}'>{$rowTitle}</a>";
                                                                   
                                                                } else if (trim($row['title']) != '' and trim($row['murl']) == '') {

                                                                    $title = "<a class='Imglink' >{$rowTitle}</a>";
                                                                    
                                                                } 

                                                           }

                                                        ?>

                                                            <?php if($media_type=='image' or $media_type=='video') :?>

                                                                    <?php if ($open_link_in == 1): ?>
                                                                        <a data-rel="<?php echo $randOmeRel; ?>"  data-type="<?php echo $media_type;?>" data-overlay="1" data-title="<?php echo $title; ?>" class="thumbnail_ <?php echo $randOmVlBox; ?> <?php if($media_type=='video'):?>   iframe <?php endif;?>" data-categories="<?php echo $row['mtag'];?>" href="<?php if($media_type=='video'):?><?php echo $embed_url; ?> <?php else:?><?php echo $outputimgmain; ?><?php endif;?>" >
                                                                            <div class="thum_div figure">

                                                                                <img src="<?php echo $outputimg;?>" alt="<?php echo $rowTitle; ?>"  title="<?php echo $rowTitle;?>" />
                                                                                 <div class="<?php echo $hoverClass;?>"></div>

                                                                             </div>  
                                                                        </a>
                                                                     <?php else: ?>

                                                                         <a   data-type="<?php echo $media_type;?>" data-title="<?php echo $title; ?>" class="thumbnail_ " data-categories="<?php echo $row['mtag'];?>" href="<?php if($media_type=='video'):?><?php echo $embed_url; ?> <?php else:?><?php echo $outputimgmain; ?><?php endif;?>" >
                                                                            <div class="thum_div figure">

                                                                                <img src="<?php echo $outputimg;?>" alt="<?php echo $rowTitle; ?>"  title="<?php echo $rowTitle; ?>" />
                                                                                 <div class="<?php echo $hoverClass;?>"></div>

                                                                             </div>  
                                                                        </a>
                                                                     <?php endif;?>

                                                             <?php else:?>
                                                                 <a   data-type="<?php echo $media_type;?>" class="thumbnail_ " data-categories="<?php echo $row['mtag'];?>" href="<?php echo $row['murl']; ?>" >
                                                                    <div class="thum_div figure">

                                                                        <img src="<?php echo $outputimg;?>" alt="<?php echo $rowTitle; ?>"  title="<?php echo $rowTitle;?>" />
                                                                         <div class="<?php echo $hoverClass;?>"></div>

                                                                     </div>  
                                                                </a>

                                                             <?php endif;?>

                                                            <?php } ?>   

                                                 <?php } ?>   

                                                </div><!-- .thumbnail_wrap end -->
                                                 
                                        </div>   

                                    </div>
                                
                                    <script>
                                        var $n = jQuery.noConflict();
                                        <?php $uniqId = uniqid(); ?>
                                         var uniqObj<?php echo $uniqId ?> = $n("a[data-rel='<?php echo $randOmeRel; ?>']");

                                        $n(document).ready(function() {



                                                var func=$n('#<?php echo $rand_Num_td;?>').filterMediank({

                                                    thumbWidth: <?php echo $settings['imagewidth'];?>,
                                                    thumbHeight: <?php echo $settings['imageheight'];?>,
                                                    thumbsSpacing:<?php echo $settings['imageMargin'];?>,
                                                    galleryId:"<?php echo $rand_var_name;?>",
                                                    backgroundColor:"<?php echo $settings['BackgroundColor'];?>",
                                                    uniqueObjId:"uniqObj<?php echo $uniqId ?>",
                                                });
                                                $n('#<?php echo $rand_var_name;?>').css('visibility','visible')

                                     var globalTimer = null;

                                        $n(window).resize(function() {
                                            clearTimeout(globalTimer);
                                            globalTimer = setTimeout(doneResize, 500);
                                        });

                                    function doneResize(){

                                      func.resizeWin('<?php echo $rand_var_name;?>');
                                    }

                                     $n(".<?php echo $randOmVlBox; ?>").fancybox_fp({

                                            'overlayColor':'#000000',
                                            'padding': 3,
                                            'margin': 20,
                                            'autoScale': true,
                                            'autoDimensions':true,
                                            'uniqObj':uniqObj<?php echo $uniqId; ?>,
                                            'uniqRel':'<?php echo $randOmeRel; ?>',
                                            'transitionIn':'fade',
                                            'transitionOut':'fade',
                                            'titlePosition': 'outside',
                                             'cyclic':true,
                                            'hideOnContentClick':false,
                                            'width' : 650,
                                            'height' : 400,
                                             'titleFormat': function(title, currentArray, currentIndex, currentOpts) {

                                                    var currtElem = $n('#<?php echo $rand_Num_td; ?> a[href="' + currentOpts.href + '"]');
                                                            var isoverlay = $n(currtElem).attr('data-overlay')

                                                     if (isoverlay == "1" && $n.trim(title) != ""){
                                                            return '<span id="fancybox_fp-title-over">' + title + '</span>';
                                                    }
                                                    else{
                                                         return '';
                                                    }

                                                    }
                                            });

                                    });  
                                    
                                    
                                 


                                  </script>

                            </div>

                        </div>   
                       
             <?php } ?>

        <div class="clear"></div>
	           
 <?php
    	$output = ob_get_clean ();
	return $output;
}
function rfp_e_portfolio_get_wp_version() {
	global $wp_version;
	return $wp_version;
}

// also we will add an option function that will check for plugin admin page or not
function rfp_responsive_portfolio_plus_lightbox_is_plugin_page() {
	$server_uri = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
	
	foreach ( array ('responsive_portfolio_with_lightbox_media_management','responsive_portfolio_with_lightbox'
	) as $allowURI ) {
		if (stristr ( $server_uri, $allowURI ))
			return true;
	}
	return false;
}

// add media WP scripts
function rfp_responsive_portfolio_plus_lightbox_admin_scripts_init() {
    
	if (rfp_responsive_portfolio_plus_lightbox_is_plugin_page ()) {
		// double check for WordPress version and function exists
		if (function_exists ( 'wp_enqueue_media' ) && version_compare ( rfp_e_portfolio_get_wp_version (), '3.5', '>=' )) {
			// call for new media manager
			wp_enqueue_media ();
		}
		wp_enqueue_style ( 'media' );
                 wp_enqueue_style( 'wp-color-picker' );
                wp_enqueue_script( 'wp-color-picker' );
                
	}
}
?>