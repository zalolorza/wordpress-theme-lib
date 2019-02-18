<?php


$colors = array(
  'primary'=>'#2960AD',
  'secondary'=>'#ef3f35',
  'primary_login'=>'#2960AD',
  'secondary_login'=>'#ef3f35',
  'primary_bar'=>'#2960AD',
  'secondary_bar'=>'#ef3f35',
  'allow_settings'=>false,
  'admin_logo'=>'/static/admin-logo.svg',
  'login_logo'=>'/static/login-logo.svg'
);

if(defined('ADMIN_UI')){

  foreach (ADMIN_UI as $key => $color){
    if($key == 'primary'){
      $colors['primary_login'] = ADMIN_UI['primary_login'] ?: $color; 
      $colors['primary_bar'] = ADMIN_UI['primary_bar'] ?: $color; 
    } else if ($key == 'secondary'){
      $colors['secondary_login'] = ADMIN_UI['secondary_login'] ?: $color; 
      $colors['secondary_bar'] = ADMIN_UI['secondary_bar'] ?: $color; 
    } 
    $colors[$key] = $color;
  }
  
} 

define('FAU_COLORS',$colors);

/*
Fancy Admin UI
*/

if(FAU_COLORS['allow_settings']){
  include_once('inc/fau_settings.php');
}



function fau_login_theme_style() {

  if(get_option( 'fau_primary_color') != ""):
    $fau_primary    = get_option( 'fau_primary_color');
  else :
    $fau_primary    = FAU_COLORS['primary_login'];
  endif;

 

  if(get_option( 'fau_secondary_color') != ""):
    $fau_secondary  = get_option( 'fau_secondary_color');
  else :
    $fau_secondary    = FAU_COLORS['secondary_login'];
  endif;

  wp_enqueue_style(
    'fau-login-style',
    get_stylesheet_directory_uri() . '/lib/fancy-admin-ui/css/fau_styles_login.css'
	);

  $login_css = "
    body, html {
      background: {$fau_primary};
    }
    .login h1 a {
      background-color: transparent !important; 
        background-image: url(".get_stylesheet_directory_uri().FAU_COLORS['login_logo'].") !important;
        -webkit-background-size: 84px;
        background-size: 84px;
        background-position: center top;
    }
  ";
  wp_add_inline_style( 'fau-login-style', $login_css );
}
add_action( 'login_enqueue_scripts', 'fau_login_theme_style' );

function fau_admin_bar_theme_style() {

  
  if(get_option( 'fau_primary_color') != ""):
    $fau_primary    = get_option( 'fau_primary_color');
  else :
    $fau_primary    = FAU_COLORS['primary_bar'];
  endif;
  if(get_option( 'fau_secondary_color') != ""):
    $fau_secondary  = get_option( 'fau_secondary_color');
  else :
    $fau_secondary    = FAU_COLORS['secondary_bar'];
  endif;

  wp_enqueue_style(
		'fau-admin-bar-style',
		get_stylesheet_directory_uri() . '/lib/fancy-admin-ui/css/fau_styles_adminbar.css'
  );
  

  $admin_bar_css = "

  .wp-admin #wpadminbar #wp-admin-bar-site-name > .ab-item {
    background-image: url(".get_stylesheet_directory_uri().FAU_COLORS['admin_logo'].") !important;
  }

    #wpadminbar {
      background: {$fau_primary};
    }

    #wpadminbar .menupop .ab-sub-wrapper,#wpadminbar .shortlink-input {
      background: {$fau_primary};
    }

  ";
  wp_add_inline_style( 'fau-admin-bar-style', $admin_bar_css );
}
add_action( 'admin_enqueue_scripts', 'fau_admin_bar_theme_style' );

function fau_admin_theme_style() {

  if(get_option( 'fau_primary_color') != ""):
    $fau_primary    = get_option( 'fau_primary_color');
  else :
    $fau_primary    = FAU_COLORS['primary'];
  endif;
  if(get_option( 'fau_secondary_color') != ""):
    $fau_secondary  = get_option( 'fau_secondary_color');
  else :
    $fau_secondary    = FAU_COLORS['secondary'];
  endif;

  wp_enqueue_style(
		'fau-admin-style',
		get_stylesheet_directory_uri() . '/lib/fancy-admin-ui/css/fau_styles_admin.css'
	);

  $admin_css = "

      #wpadminbar .quicklinks #wp-admin-bar-WPML_ALS .ab-empty-item, #wpadminbar .quicklinks #wp-admin-bar-wp-rocket .ab-empty-item, #wpadminbar .quicklinks .ab-empty-item {
        height: 46px!important;
        line-height: 46px;
        color: #fff!important;
    }
    
    a,
    input[type=checkbox]:checked:before,
    .view-switch a.current:before {
      color: {$fau_primary}
    }

    a:hover {
      color: {$fau_secondary}
    }

    #adminmenu li a:focus div.wp-menu-image:before,#adminmenu li.opensub div.wp-menu-image:before,#adminmenu li:hover div.wp-menu-image:before {
      color:  {$fau_primary}!important;
    }

    #adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head,#adminmenu .wp-menu-arrow,#adminmenu .wp-menu-arrow div,#adminmenu li.current a.menu-top,#adminmenu li.wp-has-current-submenu a.wp-has-current-submenu,.folded #adminmenu li.current.menu-top,.folded #adminmenu li.wp-has-current-submenu,/* Hover actions */
    #adminmenu li.menu-top:hover,#adminmenu li.opensub>a.menu-top,#adminmenu li>a.menu-top:focus {
      background: {$fau_primary};
      background:#FFF
    }

    #adminmenu .opensub .wp-submenu li.current a,#adminmenu .wp-submenu li.current,#adminmenu .wp-submenu li.current a,#adminmenu .wp-submenu li.current a:focus,#adminmenu .wp-submenu li.current a:hover,#adminmenu a.wp-has-current-submenu:focus+.wp-submenu li.current a,#adminmenu .wp-submenu .wp-submenu-head,/* Dashicons */
    #adminmenu .current div.wp-menu-image:before,#adminmenu .wp-has-current-submenu div.wp-menu-image:before,#adminmenu a.current:hover div.wp-menu-image:before,#adminmenu a.wp-has-current-submenu:hover div.wp-menu-image:before,#adminmenu li.wp-has-current-submenu:hover div.wp-menu-image:before, #adminmenu li:hover div.wp-menu-image:before {
      color: {$fau_primary}
    }

    #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu div.wp-menu-name {
      color: {$fau_primary}
    }

    .wrap .add-new-h2,.wrap .add-new-h2:active {
      background: {$fau_primary};
    }

    .wrap .add-new-h2:hover {
      background: {$fau_secondary}
    }

    div.updated {
      border-left: 5px solid  {$fau_primary};
    }

    .wp-core-ui .button:hover,.wp-core-ui .button-secondary:hover,.wp-core-ui .button-primary {
      background: {$fau_primary};
    }

    .wp-core-ui .button-primary.focus, .wp-core-ui .button-primary.hover, .wp-core-ui .button-primary:focus, .wp-core-ui .button-primary:hover {
      background: {$fau_primary};
      box-shadow: 0 1px 0 {$fau_primary}, 0 0 2px 1px {$fau_primary};
    }

    .composer-switch a,.composer-switch a:visited,.composer-switch a.wpb_switch-to-front-composer,.composer-switch a:visited.wpb_switch-to-front-composer,.composer-switch .logo-icon {
      background-color: {$fau_primary}!important
    }

    .composer-switch .vc-spacer, .composer-switch a.wpb_switch-to-composer:hover, .composer-switch a:visited.wpb_switch-to-composer:hover, .composer-switch a.wpb_switch-to-front-composer:hover, .composer-switch a:visited.wpb_switch-to-front-composer:hover {
      background-color:  {$fau_secondary}!important
    }

  ";
  wp_add_inline_style( 'fau-admin-style', $admin_css );
}
add_action( 'admin_enqueue_scripts', 'fau_admin_theme_style' );


// Remove default HTML height on the admin bar callback
function fui_admin_bar_style() {
  if ( is_admin_bar_showing() ) {
?>
  <style type="text/css" media="screen">
    html { margin-top: 46px !important; }
    * html body { margin-top: 46px !important; }
  </style>
<?php } }
add_theme_support( 'admin-bar', array( 'callback' => 'fui_admin_bar_style' ) );
