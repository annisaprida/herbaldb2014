<?php session_start();
	unset($lang);
	if($_SESSION[mylang][key]!='en') $_SESSION[mylang][key] = 'id';
	$lang	=	$_SESSION[mylang][key];
	require_once 'lang_db.php';
    require_once 'functions/functions.php';

    //if($_SESSION[logged_in]==1) {
	
		if($_POST[form]=='addcomment') {
			if($_SESSION[logged_in]==1) {
				foreach($_POST as $key=>$data) {
					if($key!='add' and $key!='form') {
						if($data!='') $arr_data[$key] = $data;  
						else $blank_field = true;
					}
				}
				
				if($blank_field) failed_note($_SESSION[lang][blank_field_notif][$lang]);		
				else {
					//debug($_POST);
					//debug($arr_data);
					$arr_data[comspe_insert_date]     =   date("Y-m-d-h:i-a");
					$insert = dbInsertRow($conn,$arr_data,"comments_species");
					if($insert) success_note($_SESSION[lang][success_add_comment][$lang]);
					else failed_note($_SESSION[lang][failed_add_comment][$lang]);					
				}
			}
			else failed_note($_SESSION[lang][not_logged_in_notif][$lang]);
		}
    
        // EKSEKUSI DELETE
        else if($_GET[act]=='del') {
            $q_del = dbQuery($conn,"delete from ".$_GET[tbl]." where ".$_GET[field]."_id=".$_GET[id]."");
            if($q_del)
                success_note($_SESSION[lang][success_del_data][$lang]);
            else 
                failed_note($_SESSION[lang][failed_del_data][$lang]);
        }
    
        // EKSEKUSI VERIFIKASI
        else if($_GET[act]=='ver') {
            $q_del = dbQuery($conn,"update ".$_GET[tbl]." set ".$_GET[field]."_verified_by=".$_SESSION[use_id].", ".$_GET[field]."_verified_date='".date("Y-m-d-h:i-a")."' where ".$_GET[field]."_id=".$_GET[id]."");
            if($q_del)
                success_note($_SESSION[lang][success_verify_data][$lang]);
            else 
                failed_note($_SESSION[lang][failed_verify_data][$lang]);
        }
    
        // EKSEKUSI UNVERIFIKASI
        else if($_GET[act]=='unver') {
            $q_del = dbQuery($conn,"update ".$_GET[tbl]." set ".$_GET[field]."_verified_by=-".$_SESSION[use_id].", ".$_GET[field]."_verified_date='".date("Y-m-d-h:i-a")."' where ".$_GET[field]."_id=".$_GET[id]."");
            if($q_del)
                success_note($_SESSION[lang][success_unverify_data][$lang]);
            else 
                failed_note($_SESSION[lang][failed_unverify_data][$lang]);
        }       
    
        $spe_species_id = $_GET[speciesid];
        
        $spe_id = GetFieldFromTable("spe_id","species"," where spe_species_id='".$spe_species_id."'",$conn);
        $spe_speciesname = GetFieldFromTable("spe_speciesname","species"," where spe_id='".$spe_id."'",$conn);
        
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title> <? echo $_SESSION[lang][web_title][$lang]; ?> </title>
<link rel="stylesheet" href="functions/modules/tinyaccordion/style.css" type="text/css" />
</head>
<div id="options">
	<a href="javascript:parentAccordion.pr(1)">Expand All</a> | <a href="javascript:parentAccordion.pr(-1)">Collapse All</a>
</div>

<ul class="acc" id="acc">
	<li>
		<h3><?php echo $spe_species_id.' ('.$spe_speciesname.')';?> |  <? echo $_SESSION[lang][localname_val][$lang]; ?></h3>
		<div class="acc-section">
			<div class="acc-content">
            <?php
                
                $sql = "select * from localname where spe_id='".$spe_id."' order by loc_localname asc";
                $q = dbSelectAssoc($conn,$sql);
                
                echo '<center><table border=0 width=100%>
                        <tr style=background-color:#dd5;>
                            <td style=text-align:left;padding-left:10px;><br>No.<br><br></td>
                            <td style=text-align:left;padding-left:10px;>'.$_SESSION[lang][localname_val][$lang].'</td>
                            <td style=text-align:left;padding-left:10px;>Region</td>
                            <td style=text-align:left;padding-left:10px;>Status</td>
                            <td style=text-align:left;padding-left:10px;>'.$_SESSION[lang][action_val][$lang].'</td>
                        </tr>';
                        
                if(count($q)>0) {
                
                    $ii = 1;
                    foreach($q as $r) {
                        
                        if($ii%2==1) $col = '#eee';
                        else $col = '#fff';
                        
                        $del_link = '
                                    <a href="popdetails.php?speciesid='.$spe_species_id.'&act=del&tbl=localname&field=loc&id='.$r[loc_id].'">
                                        <img style="padding:3px;" src="images/cancel_16.png" alt="delete" title="delete localname" onclick="return confirm(\''.$_SESSION[lang][del_confirm][$lang].'\')">
                                    </a>';
                                
                
                        if($r[loc_verified_by]>0) {
                            $status = '<span style=color:green;>'.$_SESSION[lang][field_verified][$lang].' <a target=blank href="index.php?v=profile&id='.$r[loc_verified_by].'">'.GetFieldFromTable("use_fullname","users"," where use_id=".$r[loc_verified_by]."",$conn).'</a></span>';
                            $ver_link = '
                                <a href="popdetails.php?speciesid='.$spe_species_id.'&act=unver&tbl=localname&field=loc&id='.$r[loc_id].'">
                                    <img style="padding:3px;" src="images/circle_red.png" alt="unverify" title="unverify localname" onclick="return confirm(\''.$_SESSION[lang][unver_confirm][$lang].'\')">
                                </a>
                            ';
                        }
                        else  if($_SESSION[rol_id]!=''){
                            $status = '<span style=color:red;>'.$_SESSION[lang][field_notverified][$lang].'</span>';
                            $ver_link = '
                                <a href="popdetails.php?speciesid='.$spe_species_id.'&act=ver&tbl=localname&field=loc&id='.$r[loc_id].'">
                                    <img style="padding:3px;" src="images/circle_green.png" alt="verify" title="verify localname" onclick="return confirm(\''.$_SESSION[lang][ver_confirm][$lang].'\')">
                                </a>
                            ';
                        }
                        
                        if($_SESSION[rol_id]!=1 and $_SESSION[rol_id]!=2) {
                            $del_link = '-';
                            $ver_link = '-';
                        }
                        
                        echo '
                            <tr style=background-color:'.$col.';>
                                <td style=text-align:left;padding-left:10px;><br>'.$ii++.'.<br><br></td>
                                <td style=text-align:left;padding-left:10px;>'.$r[loc_localname].'</td>
                                <td style=text-align:left;padding-left:10px;>'.$r[loc_region].'</td>
                                <td style=text-align:left;padding-left:10px;>'.$status.'</td>
                                <td style=text-align:left;padding-left:10px;>
                                    '.$ver_link.'
                                    '.$del_link.'
                                </td>  
                            </tr>';                        
                    }

                }
                else echo '
                        <tr><td colspan=10 style=text-align:center;><br>'.$_SESSION[lang][data_not_exist][$lang].'<br><br></td></tr>';
                
                echo '</table></center>';

            ?>
			</div>
		</div>

	</li>	
	<li>
		<h3><?php echo $spe_species_id.' ('.$spe_speciesname.')';?> |  <? echo $_SESSION[lang][aliases_val][$lang]; ?></h3>
		<div class="acc-section">
			<div class="acc-content">
            <?php
                
                $sql = "select * from aliases where spe_id='".$spe_id."' order by ali_speciesname asc";
                $q = dbSelectAssoc($conn,$sql);
                
                echo '<center><table border=0 width=100%>
                        <tr style=background-color:#dd5;>
                            <td style=text-align:left;padding-left:10px;><br>No.<br><br></td>
                            <td style=text-align:left;padding-left:10px;>'.$_SESSION[lang][name_alias_val][$lang].'</td>
                            <td style=text-align:left;padding-left:10px;>'.$_SESSION[lang][founder_alias_val][$lang].'</td>
                            <td style=text-align:left;padding-left:10px;>'.$_SESSION[lang][varietas_alias_val][$lang].'</td>
                            <td style=text-align:left;padding-left:10px;>'.$_SESSION[lang][ref_val][$lang].'</td>
                            <td style=text-align:left;padding-left:10px;>Status</td>
                            <td style=text-align:left;padding-left:10px;>'.$_SESSION[lang][action_val][$lang].'</td>
                        </tr>';
                        
                if(count($q)>0) {
                
                    $ii = 1;
                    foreach($q as $r) {
                        
                        if($ii%2==1) $col = '#eee';
                        else $col = '#fff';
                        
                        $ref_name = GetFieldFromTable("ref_name","ref"," where ref_id=".$r[ref_id]."",$conn);
                        
                        $del_link = '
                                    <a href="popdetails.php?speciesid='.$spe_species_id.'&act=del&tbl=aliases&field=ali&id='.$r[ali_id].'">
                                        <img style="padding:3px;" src="images/cancel_16.png" alt="delete" title="delete aliases" onclick="return confirm(\''.$_SESSION[lang][del_confirm][$lang].'\')">
                                    </a>';
                                
                
                        if($r[ali_verified_by]>0) {
                            $status = '<span style=color:green;>'.$_SESSION[lang][field_verified][$lang].' <a target=blank href="index.php?v=profile&id='.$r[ali_verified_by].'">'.GetFieldFromTable("use_fullname","users"," where use_id=".$r[ali_verified_by]."",$conn).'</a></span>';
                            $ver_link = '
                                <a href="popdetails.php?speciesid='.$spe_species_id.'&act=unver&tbl=aliases&field=ali&id='.$r[ali_id].'">
                                    <img style="padding:3px;" src="images/circle_red.png" alt="unverify" title="unverify aliases" onclick="return confirm(\''.$_SESSION[lang][unver_confirm][$lang].'\')">
                                </a>
                            ';
                        }
                        else  if($_SESSION[rol_id]!=''){
                            $status = '<span style=color:red;>'.$_SESSION[lang][field_notverified][$lang].'</span>';
                            $ver_link = '
                                <a href="popdetails.php?speciesid='.$spe_species_id.'&act=ver&tbl=aliases&field=ali&id='.$r[ali_id].'">
                                    <img style="padding:3px;" src="images/circle_green.png" alt="verify" title="verify aliases" onclick="return confirm(\''.$_SESSION[lang][ver_confirm][$lang].'\')">
                                </a>
                            ';
                        }
                        
                        if($_SESSION[rol_id]!=1 and $_SESSION[rol_id]!=2) {
                            $del_link = '-';
                            $ver_link = '-';
                        }
                        
                        echo '
                            <tr style=background-color:'.$col.';>
                                <td style=text-align:left;padding-left:10px;><br>'.$ii++.'.<br><br></td>
                                <td style=text-align:left;padding-left:10px;>'.$r[ali_speciesname].'</td>
                                <td style=text-align:left;padding-left:10px;>'.$r[ali_foundername].'</td>
                                <td style=text-align:left;padding-left:10px;>'.$r[ali_varietyname].'</td>
                                <td style=text-align:left;padding-left:10px;>'.$ref_name.'</td>
                                <td style=text-align:left;padding-left:10px;>'.$status.'</td>
                                <td style=text-align:left;padding-left:10px;>
                                    '.$ver_link.'
                                    '.$del_link.'
                                </td>  
                            </tr>';                        
                    }

                }
                else echo '
                        <tr><td colspan=10 style=text-align:center;><br>'.$_SESSION[lang][data_not_exist][$lang].'<br><br></td></tr>';
                
                echo '</table></center>';

            ?>
			</div>
		</div>
	</li>
	<li>
		<h3><?php echo $spe_species_id.' ('.$spe_speciesname.')';?> |  <? echo $_SESSION[lang][khasiat_val][$lang]; ?></h3>
		<div class="acc-section">
			<div class="acc-content">
            <?php
			///*
                
                $sql = "SELECT virtue.* , herbal_part.*
                        FROM virtue, herbal_part
                        WHERE virtue.hp_code = herbal_part.hp_code 
                        AND virtue.spe_id='".$spe_id."' order by herbal_part.hp_part_name asc";
                $q = dbSelectAssoc($conn,$sql);
                
                echo '<center><table border=0 width=100%>
                        <tr style=background-color:#dd5;>
                            <td style=text-align:left;padding-left:10px;><br>No.<br><br></td>
                            <td style=text-align:left;padding-left:10px;>'.$_SESSION[lang][vir_type_val][$lang].'</td>
                            <td style=text-align:left;padding-left:10px;>'.$_SESSION[lang][herbal_part_val][$lang].'</td>
                            <td style=text-align:left;padding-left:10px;>'.$_SESSION[lang][vir_value_val][$lang].'</td>
                            <td style=text-align:left;padding-left:10px;>'.$_SESSION[lang][vir_value_en_val][$lang].'</td>
                            <td style=text-align:left;padding-left:10px;>'.$_SESSION[lang][vir_value_latin_val][$lang].'</td>
                            <td style=text-align:left;padding-left:10px;>'.$_SESSION[lang][ref_val][$lang].'</td>
                            <td style=text-align:left;padding-left:10px;>Status</td>
                            <td style=text-align:left;padding-left:10px;>'.$_SESSION[lang][action_val][$lang].'</td>
                        </tr>';
                        
                if(count($q)>0) {
                
                    $ii = 1;
                    foreach($q as $r) {
                        
                        if($ii%2==1) $col = '#eee';
                        else $col = '#fff';
                        
                        $ref_name = GetFieldFromTable("ref_name","ref"," where ref_id=".$r[ref_id]."",$conn);
                        $herbal_part = GetFieldFromTable("hp_part_name","herbal_part"," where hp_code=".$r[hp_code]."",$conn);
                        
                        $del_link = '
                                <a href="popdetails.php?speciesid='.$spe_species_id.'&act=del&tbl=virtue&field=vir&id='.$r[vir_id].'">
                                    <img style="padding:3px;" src="images/cancel_16.png" alt="delete" title="delete virtue" onclick="return confirm(\''.$_SESSION[lang][del_confirm][$lang].'\')">
                                </a>';
                                
                
                        if($r[vir_verified_by]>0) {
                            $status = '<span style=color:green;>'.$_SESSION[lang][field_verified][$lang].' <a target=blank href="index.php?v=profile&id='.$r[vir_verified_by].'">'.GetFieldFromTable("use_fullname","users"," where use_id=".$r[vir_verified_by]."",$conn).'</a></span>';
                            $ver_link = '
                                <a href="popdetails.php?speciesid='.$spe_species_id.'&act=unver&tbl=virtue&field=vir&id='.$r[vir_id].'">
                                    <img style="padding:3px;" src="images/circle_red.png" alt="unverify" title="unverify virtue" onclick="return confirm(\''.$_SESSION[lang][unver_confirm][$lang].'\')">
                                </a>
                            ';
                        }
                        else  if($_SESSION[rol_id]!=''){
                            $status = '<span style=color:red;>'.$_SESSION[lang][field_notverified][$lang].'</span>';
                            $ver_link = '
                                <a href="popdetails.php?speciesid='.$spe_species_id.'&act=ver&tbl=virtue&field=vir&id='.$r[vir_id].'">
                                    <img style="padding:3px;" src="images/circle_green.png" alt="verify" title="verify virtue" onclick="return confirm(\''.$_SESSION[lang][ver_confirm][$lang].'\')">
                                </a>
                            ';
                        }
                        
                        if($_SESSION[rol_id]!=1 and $_SESSION[rol_id]!=2) {
                            $del_link = '-';
                            $ver_link = '-';
                        }
                        
                        echo '
                            <tr style=background-color:'.$col.';>
                                <td style=text-align:left;padding-left:10px;><br>'.$ii++.'.<br><br></td>
                                <td style=text-align:left;padding-left:10px;>'.$r[vir_type].'</td>
                                <td style=text-align:left;padding-left:10px;>'.$herbal_part.'</td>
                                <td style=text-align:left;padding-left:10px;>'.$r[vir_value].'</td>
                                <td style=text-align:left;padding-left:10px;>'.$r[vir_value_en].'</td>
                                <td style=text-align:left;padding-left:10px;>'.$r[vir_value_latin].'</td>
                                <td style=text-align:left;padding-left:10px;>'.$ref_name.'</td>
                                <td style=text-align:left;padding-left:10px;>'.$status.'</td>
                                <td style=text-align:left;padding-left:10px;>
                                    '.$ver_link.'
                                    '.$del_link.'
                                </td>  
                            </tr>';                        
                    }

                }
                else echo '
                        <tr><td colspan=10 style=text-align:center;><br>'.$_SESSION[lang][data_not_exist][$lang].'<br><br></td></tr>';
                
                echo '</table></center>';
			//*/
            ?>
			</div>
		</div>
	</li>
	<li>

		<h3><?php echo $spe_species_id.' ('.$spe_speciesname.')';?> |  <? echo $_SESSION[lang][photos_val][$lang]; ?></h3>
		<div class="acc-section">
			<div class="acc-content">
            <?php
                
                $sql = "select * from species_photos where spe_id='".$spe_id."' order by sph_id asc";
                $q = dbSelectAssoc($conn,$sql);
                
                echo '<center>
					<table border=0 width=100%>';
                        
                if(count($q)>0) {
                
                    $ii = 1;
                    foreach($q as $r) {
                        
                        if($ii%2==1) $col = '#eee';
                        else $col = '#fff';
                        
                        $del_link = '
                                    <a href="popdetails.php?speciesid='.$spe_species_id.'&act=del&tbl=species_photos&field=sph&id='.$r[sph_id].'">
                                        <img style="padding:3px;" src="images/cancel_16.png" alt="delete" title="delete species_photos" onclick="return confirm(\''.$_SESSION[lang][del_confirm][$lang].'\')">
                                    </a>';
                                
                
                        if($r[sph_verified_by]>0) {
                            $status = '<span style=color:green;>'.$_SESSION[lang][field_verified][$lang].' <a target=blank href="index.php?v=profile&id='.$r[sph_verified_by].'">'.GetFieldFromTable("use_fullname","users"," where use_id=".$r[sph_verified_by]."",$conn).'</a></span>';
                            $ver_link = '
                                <a href="popdetails.php?speciesid='.$spe_species_id.'&act=unver&tbl=species_photos&field=sph&id='.$r[sph_id].'">
                                    <img style="padding:3px;" src="images/circle_red.png" alt="unverify" title="unverify species_photos" onclick="return confirm(\''.$_SESSION[lang][unver_confirm][$lang].'\')">
                                </a>
                            ';
                        }
                        else  if($_SESSION[rol_id]!=''){
                            $status = '<span style=color:red;>'.$_SESSION[lang][field_notverified][$lang].'</span>';
                            $ver_link = '
                                <a href="popdetails.php?speciesid='.$spe_species_id.'&act=ver&tbl=species_photos&field=sph&id='.$r[sph_id].'">
                                    <img style="padding:3px;" src="images/circle_green.png" alt="verify" title="verify species_photos" onclick="return confirm(\''.$_SESSION[lang][ver_confirm][$lang].'\')">
                                </a>
                            ';
                        }
                        
                        if($_SESSION[rol_id]!=1 and $_SESSION[rol_id]!=2) {
                            $del_link = '-';
                            $ver_link = '-';
                        }

						if($ii%3==1) echo '<tr style=background-color:'.$col.';>';
						echo '<td align=center>
									<br><img alt="Foto '.$r[name].'" 
										src="'._SPECIES_FILES_PATH.'/'.$spe_id.'/'.$r[sph_filename].'" width="200px" height="200px">
									<br><br><b>'.$r[sph_note].'</b><br>
									<br>Status : '.$status.'
									<br><br>'.$ver_link.'  '.$del_link.'<br><br>
							  </td>';
						if($ii%3==0) echo '</tr>';

						$ii++;
                    }

                }
                else echo '
                        <tr><td colspan=10 style=text-align:center;><br>'.$_SESSION[lang][data_not_exist][$lang].'<br><br></td></tr>';
                
                echo '</table></center>';

            ?>
			</div>
		</div>

	</li>
	<li>
		<h3><?php echo $spe_species_id.' ('.$spe_speciesname.')';?> |  <? echo $_SESSION[lang][content_val][$lang]; ?></h3>
		<div class="acc-section">
			<div class="acc-content">
            <?php
                
                $sql = "SELECT contents.* , speciescontent.*
                        FROM contents, speciescontent
                        WHERE speciescontent.con_id = contents.con_id
                        AND speciescontent.spe_id='".$spe_id."' order by contents.con_contentname asc";
                $q = dbSelectAssoc($conn,$sql);
                
                echo '<center><table border=0 width=100%>
                        <tr style=background-color:#dd5;>
                            <td style=text-align:left;padding-left:10px;><br>No.<br><br></td>
                            <td style=text-align:left;padding-left:10px;>'.$_SESSION[lang][content_val][$lang].'</td>
                            <td style=text-align:left;padding-left:10px;>Knapsack ID</td>
                            <td style=text-align:left;padding-left:10px;>Metabolite ID</td>
                            <td style=text-align:left;padding-left:10px;>Pubchem ID</td>
			    <td style=text-align:left;padding-left:10px;>'.$_SESSION[lang][ref_val][$lang].'</td>
                            <td style=text-align:left;padding-left:10px;>Status</td>
                            <td style=text-align:left;padding-left:10px;>'.$_SESSION[lang][action_val][$lang].'</td>
                        </tr>';
                        
                if(count($q)>0) {
                
                    $ii = 1;
                    foreach($q as $r) {
                        
                        if($ii%2==1) $col = '#eee';
                        else $col = '#fff';
                        
                        $ref_name = GetFieldFromTable("ref_name","ref"," where ref_id=".$r[ref_id]."",$conn);
                        $contgroup_name = GetFieldFromTable("contgroup_name","contentgroup"," where contgroup_id=".$r[contgroup_id]."",$conn);
                        
                        $del_link = '
                                <a href="popdetails.php?speciesid='.$spe_species_id.'&act=del&tbl=speciescontent&field=specon&id='.$r[specon_id].'">
                                    <img style="padding:3px;" src="images/cancel_16.png" alt="delete" title="delete content" onclick="return confirm(\''.$_SESSION[lang][del_confirm][$lang].'\')">
                                </a>';
                                
                
                        if($r[specon_verified_by]>0) {
                            $status = '<span style=color:green;>'.$_SESSION[lang][field_verified][$lang].' <a target=blank href="index.php?v=profile&id='.$r[specon_verified_by].'">'.GetFieldFromTable("use_fullname","users"," where use_id=".$r[specon_verified_by]."",$conn).'</a></span>';
                            $ver_link = '
                                <a href="popdetails.php?speciesid='.$spe_species_id.'&act=unver&tbl=speciescontent&field=specon&id='.$r[specon_id].'">
                                    <img style="padding:3px;" src="images/circle_red.png" alt="unverify" title="unverify content" onclick="return confirm(\''.$_SESSION[lang][unver_confirm][$lang].'\')">
                                </a>
                            ';
                        }
                        else  if($_SESSION[rol_id]!=''){
                            $status = '<span style=color:red;>'.$_SESSION[lang][field_notverified][$lang].'</span>';
                            $ver_link = '
                                <a href="popdetails.php?speciesid='.$spe_species_id.'&act=ver&tbl=speciescontent&field=specon&id='.$r[specon_id].'">
                                    <img style="padding:3px;" src="images/circle_green.png" alt="verify" title="verify content" onclick="return confirm(\''.$_SESSION[lang][ver_confirm][$lang].'\')">
                                </a>
                            ';
                        }
                        
                        if($_SESSION[rol_id]!=1 and $_SESSION[rol_id]!=2) {
                            $del_link = '-';
                            $ver_link = '-';
                        }
						
						$linked_contentname = '<a onClick="window.open(\'popdetails_content.php?con_id='.$r[con_id].'\',null, \'height=800px,width=1000px,status=yes,toolbar=no,scrollbars=yes,menubar=no,location=no\');" href=\'#\' >'.$r[con_contentname].'</a>';
                        
                        echo '
                            <tr style=background-color:'.$col.';>
                                <td style=text-align:left;padding-left:10px;><br>'.$ii++.'.<br><br></td>
                                <td style=text-align:left;padding-left:10px;>'.$linked_contentname.'</td>
                                <td style=text-align:left;padding-left:10px;>'.$r[con_knapsack_id].'</td>
                                <td style=text-align:left;padding-left:10px;>'.$r[con_metabolite_id].'</td>
                                <td style=text-align:left;padding-left:10px;>'.$r[con_pubchem_id].'</td>
                                <td style=text-align:left;padding-left:10px;>'.$ref_name.'</td>
                                <td style=text-align:left;padding-left:10px;>'.$status.'</td>
                                <td style=text-align:left;padding-left:10px;>
                                    '.$ver_link.'
                                    '.$del_link.'
                                </td>  
                            </tr>';                        
                    }

                }
                else echo '
                        <tr><td colspan=10 style=text-align:center;><br>'.$_SESSION[lang][data_not_exist][$lang].'<br><br></td></tr>';
                
                echo '</table></center>';

            ?>
			</div>
		</div>
	</li>	
	<li>

		<h3><?php echo $spe_species_id.' ('.$spe_speciesname.')';?> | <? echo $_SESSION[lang][comment_val][$lang]; ?></h3>
		<div class="acc-section">
			<div class="acc-content">
            <?php
                
                $sql = "select * from comments_species where spe_id='".$spe_id."' order by comspe_id asc";
                $q = dbSelectAssoc($conn,$sql);
                
                echo '<center><table border=0 width=98%>
                        <tr style=background-color:#dd5;>
                            <td style=text-align:left;padding-left:10px;><br>No.<br><br></td>
                            <td style=text-align:left;padding-left:10px;>'.$_SESSION[lang][date_val][$lang].'</td>
                            <td style=text-align:left;padding-left:10px;>'.$_SESSION[lang][comment_val][$lang].'</td>
                        </tr>';
                      
				$ii = 1;
                if(count($q)>0) {
				
                    foreach($q as $r) {
                        
                        if($ii%2==1) $col = 'ffe';
                        else $col = '#fafafa';
						
						$fullname			= GetFieldFromTable("use_fullname","users"," where use_id='".$r[use_id]."'",$conn);
						$linked_fullname 	= '<a target=blank href=index.php?v=profile&id='.$r[use_id].'>'.$fullname.'</a>';
						
                        echo '
                            <tr style=background-color:'.$col.';>
                                <td style=text-align:left;padding-left:10px;><br>'.$ii++.'.<br><br></td>
                                <td style=text-align:left;padding-left:10px;>'.$r[comspe_insert_date].'</td>
                                <td style=text-align:left;padding-left:10px;>
									<br>'.$linked_fullname.' '.$_SESSION[lang][say_comment_val][$lang].' :
									<br><br>
										<center><div style=margin:5px;padding:5px;background-color:#fff;text-align:justify;>'.$r[comspe_content].'</div></center>
									<br>
								</td>
                            </tr>';                        
                    }

                }
                else echo '
                        <tr><td colspan=10 style=text-align:center;><br>'.$_SESSION[lang][data_not_exist][$lang].'<br><br></td></tr>';
                
                echo '
                        <tr style=background-color:#ffe;>
						<form method=post>
                            <td style=text-align:left;padding-left:10px;><br>'.$ii.'.<br><br></td>
                            <td style=text-align:left;padding-left:10px;><br></td>
                            <td style=text-align:left;padding-left:10px;>
								<br><b>'.$_SESSION[lang][fill_comment_val][$lang].'</b><br><br>
								<textarea style=width:450px;height:100px; name=comspe_content></textarea><br>
                                <input name=form type=hidden value="addcomment"/>
                                <input name=use_id type=hidden value="'.$_SESSION[use_id].'"/>
                                <input name=spe_id type=hidden value="'.$spe_id.'"/>
                                <br><input name=add type=submit value="'.$_SESSION[lang][fill_comment_val][$lang].'"  onclick="return confirm(\''.$_SESSION[lang][field_confirm_insert][$lang].'\')" /> 
								<br><br>
							</td>
						</form>
                        </tr>				
					</table></center>';

            ?>
			</div>
		</div>

	</li>
</ul>

<script type="text/javascript" src="functions/modules/tinyaccordion/script.js"></script>

<script type="text/javascript">

var parentAccordion=new TINY.accordion.slider("parentAccordion");
parentAccordion.init("acc","h3",-1,0);

</script>

</html>    

<?php
/*
    }

    else {
        echo 'You must be logged in to access this fitur. <a href="index.php">Login</a>';
    }
	*/
    ?>
