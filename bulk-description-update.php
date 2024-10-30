<?php 
/*
Plugin Name: Bulk Slug & Description Update
Plugin URI: http://wordpress.org/extend/plugins/bulk-description-update/
Version: v1.0.2
Author: Jiayu (James) Ji
Description:If you find that updating the description or slug for category, tag or link category one by one is annoying, this is absolutely a plug-in for you. With this plug-in, you are allowed to update slug and description for multiple records in one page. Also, it allows you to use html tags inside the description.

*/

/*  
    Copyright 2011  Bulk Description Update  (email : Jiayu.ji@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if(!class_exists("BulkDescriptionUpdate")){
	class BulkDescriptionUpdate{

        private $bdu_taxonomy;
	 
	   function BulkDescriptionUpdate(){
	   }
	   
	   function displayForm(){
		$bdu_taxonomy = $_REQUEST['bdu_taxonomy'];
	?>
	<link rel="stylesheet" href="<?php echo WP_PLUGIN_URL; ?>/bulk-description-update/css/style.css" type="text/css"/>
	<fieldset>
	<legend>Bulk Description Update</legend>
	<form name="termName" action="" method="post">
	<H2>Select the term you want to update:</H2>
	<select name="bdu_taxonomy" onchange="displayTerm();">
		<option value="null" >--Please Select--</option>
		<option value="category" <?php echo $bdu_taxonomy=="category"?"selected='selected'":""?>>Category</option>
		<option value="post_tag" <?php echo $bdu_taxonomy=="post_tag"?"selected='selected'":""?>>Tag</option>
		<option value="link_category" <?php echo $bdu_taxonomy=="link_category"?"selected='selected'":""?>>Link Category</option>
	</select>
	</form>
	<?php if($_REQUEST['result'] == "success"){
	      echo '<div id="message" class="updated fade"><p><strong>Update successfully finished!! </strong></p></div>';	
	      }else if(trim($_REQUEST['result'])!=""){
	      echo '<div id="message" class="updated fade"><p><strong>Error: Slug "'.$_REQUEST['result'].'" is already in use by another term!!</strong></p></div>';
	      }
	;?>
	<?php if(trim($bdu_taxonomy)!=""&&$bdu_taxonomy != "null"){?>
	<div id="displayLayer" style="display:none"></div>
	<form name="bulk_desc_update" action="" method="post">
		<table>
		<thead>
			<tr width="100%">
				<th width="15%" class="name"><a href="edit.php?page=bulk-description-update/bulk-description-update.php&bdu_taxonomy=<?php echo $bdu_taxonomy;?>&sortby=name" title="Sorted by name">Name</a></th>
				<th width="25%" class="slug"><a href="edit.php?page=bulk-description-update/bulk-description-update.php&bdu_taxonomy=<?php echo $bdu_taxonomy;?>&sortby=slug" title="Sorted by slug">Slug</a></th>
				<th width="60%" class="desc"><a href="javascript:void();">Description</a></th>
			</tr>
		</thead>
		<tbody>
           	<?php
		        if($_REQUEST['sortby']=="slug"){
				$terms = get_terms($bdu_taxonomy,"orderby=slug&get=all");
			}else if($_REQUEST['sortby']=="name"){
				$terms = get_terms($bdu_taxonomy,"orderby=name&get=all");
			}else{
				$terms = get_terms($bdu_taxonomy,"orderby=id&get=all");
			}
			$count = 1;
			foreach($terms as $term){
			$styleClass = $count%2==1?'class="odd"':'class="even"';
			$count++;
		?>
			<tr>
			<td <?php echo $styleClass;?> align="center"><h3><?php echo $term->name; ?></h3></td>
			<td <?php echo $styleClass;?> align="center"><input name="<?php echo "s".$term->term_id?>" type="text" value="<?php echo $term->slug?>" size="25"/></td>
			<td <?php echo $styleClass;?> align="center">
				<div id="<?php echo "m".$term->term_id?>" class="showEffect"></div>
				<textarea id = "<?php echo "d".$term->term_id?>" name="<?php echo "d".$term->term_id?>" cols="65" rows="6" onfocus="displayEffect('<?php echo $term->term_id; ?>');" onkeyup="displayEffect('<?php echo $term->term_id; ?>');" onblur="undisplay('<?php echo $term->term_id; ?>');"><?php echo $term->description?></textarea>
			</td>
			</tr>
		<?php	
			}
		?>
		</tbody>
		</table>
		<input type="hidden" name="bdu_taxonomy" value="<?php echo $bdu_taxonomy ?>"/>
		<p class="submit"><input type="button" name="bdp_submit" value="Update" onclick="updateTerm();"/></p>
		<input type="hidden" name="update" value="updateTerm"/>
        </form>
	<?php } ?>
	</fieldset>
	<script type="text/javascript">
		function displayTerm(){
		 document.forms["termName"].submit();
		}
		
		function updateTerm(){
		 document.forms["bulk_desc_update"].submit();
		}
		
		function displayEffect(id){
		    var div = document.getElementById('m'+id);
		    div.innerHTML = document.getElementById('d'+id).value;
		    div.style.display = "block";
		}
		
		function undisplay(id){
		    document.getElementById('m'+id).innerHTML = "";
		    document.getElementById('m'+id).style.display = "none"; 
		}
	</script>
		 <?php
	   }
	   
	   
	}
}


function bulk_update_desc_menu() {
    
    if(class_exists("BulkDescriptionUpdate")){
	$dl_bulkDescUpdate = new BulkDescriptionUpdate();
	}
    
    if(trim($_REQUEST['update'])=="updateTerm"){
		$bdu_taxonomy = $_POST['bdu_taxonomy'];   
		    $terms = get_terms($bdu_taxonomy,"get=all");
			foreach($terms as $term){
				$slug = $_POST['s'.$term->term_id];
				$desc = $_POST['d'.$term->term_id];
				$args = array("slug" => $slug, "description" => $desc);
				$result = wp_update_term( $term->term_id, $bdu_taxonomy, $args );
				if(is_wp_error($result)) break;	
			}
			if(is_wp_error($result)){
				header("Location: edit.php?page=bulk-description-update/bulk-description-update.php&bdu_taxonomy=".$bdu_taxonomy."&result=".$slug);
			}else{
				header("Location: edit.php?page=bulk-description-update/bulk-description-update.php&bdu_taxonomy=".$bdu_taxonomy."&result=success");
			}
			die;
		}
		
		if(isset($_POST['bdu_taxonomy'])){
		     $bdu_taxonomy = $_POST['bdu_taxonomy'];   
		     header("Location: edit.php?page=bulk-description-update/bulk-description-update.php&bdu_taxonomy=".$bdu_taxonomy);
		     die;
		}
	add_submenu_page("edit.php", 'Bulk Description Update', 'Bulk Description Update', 'manage_options', __FILE__, array($dl_bulkDescUpdate, 'displayForm'));	
}

add_action('admin_menu', 'bulk_update_desc_menu');

remove_filter( 'pre_term_description', 'wp_filter_kses' );
remove_filter( 'term_description', 'wp_kses_data' );
	
?>