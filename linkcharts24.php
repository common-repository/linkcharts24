<?php

define( 'lc_VERSION', "0.1.1");

/*
Plugin Name: LinkCharts24
Plugin URI: http://sit.24stunden.de/linkcharts24
Description: This plugin calculates all outgoing links from your postings and will send the results to a blogcharts-system in future. In this first version only the outgoing links are calculated.
Author: Sebastian Schwaner
Author URI: http://sit.24stunden.de/linkcharts24
Version: 0.1.1
*/

add_action('admin_menu','lc_setGUI');

function lc_setGUI() {
     add_submenu_page( 'edit.php','LinkCharts','LinkCharts',9,__FILE__,'lc_GUI' );
}


function lc_link2Host( $link ) {
     $url = parse_url( $link );
     $host = ereg_replace("www.","",$url['host']);

     return $host;
}


function lc_GUI() {
     global $wpdb;

     $links = array();
     $own_host = lc_link2host( get_option("home") );
     $site = @$_GET['site'];

     $posts = $wpdb->get_results(" SELECT post_title,post_content FROM $wpdb->posts WHERE post_content LIKE '%<a%' ORDER BY ID DESC");

     foreach( $posts as $posts ) :
          $content = strip_tags($posts->post_content,"<a>");

          while( $start = strpos($content,"href=",$stop) ) :
               if( ! $stop = strpos($content,'"',$start+6) ) break;
               $link = substr($content,$start+6,($stop-$start)-6);
               $host = lc_link2Host($link);
               if( $host != $own_host && $host != '.' && $host != '') $links[$host]++;
          endwhile;

          $stop = 0;

     endforeach;

     arsort( $links );
     $count = count( $links );

     ?>

     <div class="wpbody">
          <div class="wrap">

               <h2>LinkCharts24</h2>

               <p>This plugins calculates all outgoing links from your postings and show the results in a ranking ordered by links.</p>

               <div style="background-color:#f1f1f8; padding:10px; -moz-border-radius:0.5em; border:1px solid gray; margin-top:20px;">
                    <b>Pages:</b>
                    <?php if( $count%50 == 0 ) $pages=$count/50; else $pages=floor($count/50)+1; ?>
                    <?php for($i=0;$i<$pages;$i++) : ?>
                    <a href="?page=linkcharts24/linkcharts24.php&site=<?php echo $i; ?>" style="padding:5px; font-weight:bold; <?php if( @$_GET['site'] == $i ) echo "font-size:16px; color:#333;"; ?>"><?php echo $i+1; ?></a>
                    <?php endfor; ?>
               </div>

               <br/>

               <?php $start = $site*50+1; $stop=$start+49; ?>

               <table class="widefat">
                    <thead>
                         <tr>
                              <th style="width:10%;">Ranking</th>
                              <th>Linked External Host</th>
                              <th>Links</th>
                         </tr>
                    </thead>
                    <tbody>
                         <?php $i=1; ?>
                         <?php while(list($key, $val) = each($links)) : ?>
                         <?php if( $i >= $start && $i <= $stop ) : ?>
                         <tr>
                              <td><?php echo $i; ?></td>
                              <td><a href="http://<?php echo $key; ?>" target="_blank"><?php echo $key; ?></a></td>
                              <td><?php echo $val; ?></td>
                         </tr>
                         <?php endif; ?>
                         <?php $i++; ?>
                         <?php endwhile; ?>

                    </tbody>
               </table>

               <p>Copyright &copy; by sit. small it-solutions.<br/>Offical homepage of the Wordpress plugin
               <b><a href="http://sit.24stunden.de/linkcharts24/">LinkCharts24</a></b> &raquo;</p>

          </div>
     </div>



<?php } ?>