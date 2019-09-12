<?php
	require_once("../includes/init.php");
	require_once("../includes/DBC.php");
	
	$page_size 		= DBC::dbescape($_POST['s']);
	$page_number 	= DBC::dbescape($_POST['p']);
	
	$url = "http://trialapi.craig.mtcdevserver.com/api/properties?api_key=3NLTTNlXsi6rBWl7nYGluOdkl2htFHug&page[number]=$page_number&page[size]=$page_size";
	
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	$response = curl_exec($ch);
	curl_close($ch);
	
	$json 			= json_decode($response);
	$current_page 	= $json->current_page;
	$last_page 		= $json->last_page;
	$data 			= $json->data;
	$total_data 	= count($data);
	$prev_page 		= $current_page-1;
	$next_page 		= $current_page+1;
	
	if($prev_page < 1){$prev_page = 1;}
	if($next_page > $last_page){$next_page = $last_page;}
	
	for($i = 0; $i < $total_data; $i++){
		$uuid 				= $data[$i]->uuid;
		$property_type_id 	= $data[$i]->property_type_id;
		$county 			= $data[$i]->county;
		$country 			= $data[$i]->country;
		$town 				= $data[$i]->town;
		$description 		= $data[$i]->description;
		$address 			= $data[$i]->address;
		$image_full 		= $data[$i]->image_full;
		$image_thumbnail 	= $data[$i]->image_thumbnail;
		$latitude 			= $data[$i]->latitude;
		$longitude 			= $data[$i]->longitude;
		$num_bedrooms 		= $data[$i]->num_bedrooms;
		$num_bathrooms 		= $data[$i]->num_bathrooms;
		$price 				= $data[$i]->price;
		$sale_type 			= $data[$i]->type;
		$created_at 		= $data[$i]->created_at;
		$updated_at 		= $data[$i]->updated_at;
		
		$property_type_id 			= $data[$i]->property_type->id;
		$property_type_title 		= $data[$i]->property_type->title;
		$property_type_description 	= $data[$i]->property_type->description;
		
		$check = DBC::dbsql("SELECT id FROM property_type WHERE id = '$property_type_id';");
		if(DBC::dbrows($check) == 0){
			DBC::dbsql("INSERT INTO property_type SET 	id = '$property_type_id',
														title = '$property_type_title',
														description = '$property_type_description';");
		}
		
		$check = DBC::dbsql("SELECT * FROM properties WHERE uuid = '$uuid';");
		if(DBC::dbrows($check) == 0){
			DBC::dbsql("INSERT INTO properties SET 	uuid = '$uuid',
													county = '$county',
													country = '$country',
													town = '$town',
													description = '$description',
													full_details_url = '',
													displayable_address = '',
													image_url = '$image_full',
													thumbnail_url = '$image_thumbnail',
													latitude = '$latitude',
													longitude = '$longitude',
													num_bedrooms = '$num_bedrooms',
													num_bathrooms = '$num_bathrooms',
													price = '$price',
													property_type_id = '$property_type_id',
													sale_type = '$sale_type',
													created_at = '$created_at',
													updated_at = '$updated_at';");
		}else{
			$getCheck 	= DBC::dbfetch($check);
			$status 	= $getCheck['status'];
			if($status == 0){
				DBC::dbsql("UPDATE properties SET 	county = '$county',
													country = '$country',
													town = '$town',
													description = '$description',
													full_details_url = '',
													displayable_address = '',
													image_url = '$image_full',
													thumbnail_url = '$image_thumbnail',
													latitude = '$latitude',
													longitude = '$longitude',
													num_bedrooms = '$num_bedrooms',
													num_bathrooms = '$num_bathrooms',
													price = '$price',
													property_type_id = '$property_type_id',
													sale_type = '$sale_type',
													created_at = '$created_at',
													updated_at = '$updated_at'
													WHERE uuid = '$uuid';");
			}
		}
	}
?>
<div class="row">
	<div class="col-md-12" align="center">
    	<li class="fa fa-angle-double-left mr-2" style="font-size: 30px; cursor: pointer;" onClick="loadProperties(1);"> <span style="font-size: 14px;">First Page</span></li>
    	<li class="fa fa-chevron-left mr-5" style="font-size: 20px; cursor: pointer;" onClick="loadProperties(<?php print $prev_page; ?>);"> <span style="font-size: 14px;">Previous Page</span></li>
    	<span style="font-size: 14px; cursor: pointer;" onClick="loadProperties(<?php print $next_page; ?>);">Next Page <li class="fa fa-chevron-right mr-2" style="font-size: 20px;"></li></span>
    	<span style="font-size: 14px; cursor: pointer;" onClick="loadProperties(<?php print $last_page; ?>);">Last Page <li class="fa fa-angle-double-right" style="font-size: 30px;"></span>
    </div>
</div>
<div class="row">
	<?php
        $properties = DBC::dbsql("SELECT * FROM properties WHERE status != '3' ORDER BY id ASC LIMIT $page_size;");
        while($getProperties = DBC::dbfetch($properties)){
            $uuid 				= $getProperties['uuid'];
            $county 			= $getProperties['county'];
            $country 			= $getProperties['country'];
            $town 				= $getProperties['town'];
            $thumbnail_url 		= $getProperties['thumbnail_url'];
            $price 				= $getProperties['price'];
            $num_bedrooms 		= $getProperties['num_bedrooms'];
            $num_bathrooms 		= $getProperties['num_bathrooms'];
            $property_type_id 	= $getProperties['property_type_id'];
            $sale_type 			= $getProperties['sale_type'];
			
			$proptype 			= DBC::dbsql("SELECT * FROM property_type WHERE id = '$property_type_id';");
			$getPropType 		= DBC::dbfetch($proptype);
			$property_type_title = $getPropType['title'];
            
            ?>
                <div class="col-md-3 mt-2 pt-2 prop_hover">
                    <div class="row" style="font-size: 12px;">
                        <div class="col-md-12 mt-2">
                            <img src="<?php print $thumbnail_url; ?>" width="100%" class="img-thumbnail" alt="Property Thumbnail" />
                        </div>
                        <div class="col-md-12" style="height: 80px;">
                            <?php print $town."<br />".$county."<br />".$country; ?>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-5"><strong>Price</strong></div>
                                <div class="col-md-7">: <?php print number_format($price, 0); ?> GBP</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-5"><strong>Prop Type</strong></div>
                                <div class="col-md-7">: <?php print $property_type_title; ?></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-5"><strong>Bedrooms</strong></div>
                                <div class="col-md-7">: <?php print $num_bedrooms; ?></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-5"><strong>Bathrooms</strong></div>
                                <div class="col-md-7">: <?php print $num_bathrooms; ?></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-5"><strong>Type</strong></div>
                                <div class="col-md-7">: <?php print ucwords($sale_type); ?></div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2 mb-2">
                            <div class="row">
                                <div class="col-md-4" align="center"><li class="fa fa-eye" style="font-size: 30px; cursor: pointer;" onClick="window.location='view-property-<?php print $uuid; ?>';"></li></div>
                                <div class="col-md-4" align="center"><li class="fa fa-pencil-square" style="font-size: 30px; cursor: pointer;" onClick="window.location='property-edit-<?php print $uuid; ?>';"></li></div>
                                <div class="col-md-4" align="center"><li class="fa fa-trash" style="font-size: 30px; cursor: pointer;" data-toggle="modal" data-target="#confirmDelete" onClick="$('#delete_uuid').val('<?php print $uuid; ?>');"></li></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
        }
    ?>
</div>
