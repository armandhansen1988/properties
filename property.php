<?php
	error_reporting(0);
	require_once("includes/init.php");
	require_once("includes/DBC.php");
	
	$type = DBC::dbescape($_GET['type']);
	$uuid = DBC::dbescape($_GET['uuid']);
	$error = 0;
	
	function generateToken() {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$charactersLength = strlen($characters);
		$token = '';
		for ($i = 0; $i < 8; $i++) {
			$token .= $characters[rand(0, $charactersLength - 1)];
		}
		$token .= "-";
		for ($i = 0; $i < 4; $i++) {
			$token .= $characters[rand(0, $charactersLength - 1)];
		}
		$token .= "-";
		for ($i = 0; $i < 4; $i++) {
			$token .= $characters[rand(0, $charactersLength - 1)];
		}
		$token .= "-";
		for ($i = 0; $i < 4; $i++) {
			$token .= $characters[rand(0, $charactersLength - 1)];
		}
		$token .= "-";
		for ($i = 0; $i < 12; $i++) {
			$token .= $characters[rand(0, $charactersLength - 1)];
		}
		return $token;
	}
	
	function generateThumbnail($img, $thumbnail_url, $width, $height, $quality = 90)
	{
		$imagick = new Imagick(realpath($img));
		$imagick->setImageFormat('jpeg');
		$imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
		$imagick->setImageCompressionQuality($quality);
		$imagick->thumbnailImage($width, $height, false, false);
		$filename_no_ext = reset(explode('.', $thumbnail_url));
		file_put_contents($filename_no_ext.'.jpg', $imagick);
	}
	
	if($_POST){
		$token 					= generateToken();
		$action_type 			= DBC::dbescape($_POST['action_type']);
		$uuid 					= DBC::dbescape($_POST['uuid']);
		$town 					= DBC::dbescape($_POST['town']);
		$county					= DBC::dbescape($_POST['county']);
		$country 				= DBC::dbescape($_POST['country']);
		$postcode 				= DBC::dbescape($_POST['postcode']);
		$displayable_address 	= DBC::dbescape($_POST['displayable_address']);
		$coordinates 			= DBC::dbescape($_POST['coordinates']);
		$description 			= DBC::dbescape($_POST['description']);
		$image 					= basename($_FILES['image']['name']);
		$num_bedrooms 			= DBC::dbescape($_POST['num_bedrooms']);
		$num_bathrooms 			= DBC::dbescape($_POST['num_bathrooms']);
		$price 					= DBC::dbescape($_POST['price']);
		$property_type 			= DBC::dbescape($_POST['property_type']);
		$sale_type 				= DBC::dbescape($_POST['sale_type']);
		
		$exCoor 				= explode(", ", $coordinates);
		$lat 					= $exCoor[0];
		$lng 					= $exCoor[1];
		$valid_ext				= array("jpg", "jpeg", "png");
		
		if($action_type == "add"){
			$unix_time 		= time();
			$image_url 		= "uploads/".time()."_".$image;
			$image_url_ext	= strtolower(pathinfo($image_url, PATHINFO_EXTENSION));
			if(!in_array($image_url_ext, $valid_ext)){
				$error = 2;
				$error_msg = "Image can only be jpg, jpeg, or png!";
			}else{
				$thumbnail_url 	= "uploads/thumb_".time()."_".$image;
				move_uploaded_file($_FILES['image']['tmp_name'], $image_url);
				generateThumbnail($image_url, $thumbnail_url, 100, 40);
				$save = DBC::dbsql("INSERT INTO properties SET 	uuid = '$token',
																county = '$county',
																country = '$country',
																town = '$town',
																postcode = '$postcode',
																description = '$description',
																displayable_address = '$displayable_address',
																image_url = '$image_url',
																thumbnail_url = '$thumbnail_url',
																latitude = '$lat',
																longitude = '$lng',
																num_bedrooms = '$num_bedrooms',
																num_bathrooms = '$num_bathrooms',
																price = '$price',
																property_type_id = '$property_type',
																sale_type = '$sale_type',
																created_at = NOW(),
																status = '1';");
				if($save){
					$error = 1;
					$error_msg = "Property added successfully";
					
					unset($town);
					unset($county);
					unset($country);
					unset($postcode);
					unset($displayable_address);
					unset($coordinates);
					unset($description);
					unset($num_bedrooms);
					unset($num_bathrooms);
					unset($price);
					unset($property_type);
					unset($sale_type);
					$type = "add";
				}else{
					$error = 2;
					$error_msg = "Error saving property. Please try again";
				}
			}
		}elseif($action_type == "edit"){
			if($image != ""){
				$unix_time		= time();
				$image_url 		= "uploads/".time()."_".$image;
				$thumbnail_url 	= "uploads/thumb_".time()."_".$image;
				$image_url_ext	= strtolower(pathinfo($image_url, PATHINFO_EXTENSION));
				if(!in_array($image_url_ext, $valid_ext)){
					$error = 2;
					$error_msg = "Image can only be jpg, jpeg, or png!";
				}else{
					move_uploaded_file($_FILES['image']['tmp_name'], $image_url);
					generateThumbnail($image_url, $thumbnail_url, 100, 40);
					DBC::dbsql("UPDATE properties SET 	image_url = '$image_url',
														thumbnail_url = '$thumbnail_url'
														WHERE uuid = '$uuid';");
				}
			}
			
			$update = DBC::dbsql("UPDATE properties SET county = '$county',
														country = '$country',
														town = '$town',
														postcode = '$postcode',
														description = '$description',
														displayable_address = '$displayable_address',
														latitude = '$lat',
														longitude = '$lng',
														num_bedrooms = '$num_bedrooms',
														num_bathrooms = '$num_bathrooms',
														price = '$price',
														property_type_id = '$property_type',
														sale_type = '$sale_type',
														updated_at = NOW(),
														status = '1'
														WHERE uuid = '$uuid';");
			if($update){
				$error = 1;
				
				unset($town);
				unset($county);
				unset($country);
				unset($postcode);
				unset($displayable_address);
				unset($coordinates);
				unset($description);
				unset($num_bedrooms);
				unset($num_bathrooms);
				unset($price);
				unset($property_type);
				unset($sale_type);
				$type = "add";
			}else{
				$error = 2;
			}
		}
	}
	
	if($type == "edit"){
		$property 				= DBC::dbsql("SELECT * FROM properties WHERE uuid = '$uuid';");
		$getProperty 			= DBC::dbfetch($property);
		$county 				= $getProperty['county'];
		$country 				= $getProperty['country'];
		$town 					= $getProperty['town'];
		$postcode 				= $getProperty['postcode'];
		$displayable_address 	= $getProperty['displayable_address'];
		$description 			= $getProperty['description'];
		$image_url 				= $getProperty['image_url'];
		$thumbnail_url 			= $getProperty['thumbnail_url'];
		$latitude 				= $getProperty['latitude'];
		$longitude 				= $getProperty['longitude'];
		$num_bedrooms 			= $getProperty['num_bedrooms'];
		$num_bathrooms 			= $getProperty['num_bathrooms'];
		$price 					= $getProperty['price'];
		$property_type_id 		= $getProperty['property_type_id'];
		$sale_type 				= $getProperty['sale_type'];
		
		$proptype 					= DBC::dbsql("SELECT * FROM property_type WHERE id = '$property_type_id';");
		$getPropType 				= DBC::dbfetch($proptype);
		$property_type_title 		= $getPropType['title'];
		$property_type_description 	= $getPropType['description'];
		if($displayable_address == ""){
			$geolocate = "$town, $county, $country";
		}else{
			$geolocate = $displayable_address;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php print '
	<link rel="stylesheet" href="css/bootstrap.min.css" />
	<link rel="stylesheet" href="css/form-validation.css" />
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
	<link rel="stylesheet" href="css/styles.css" />
'; ?>
<title>Armand's Properties</title>
</head>
<body class="bg-light">
	<div class="container">
    	<div class="row">
        	<div class="col-md-12">
            	<button type="button" class="btn btn-primary" onclick="window.location='./'">Back</button>
            </div>
        </div>
  		<div class="py-2 text-center">
    		<h2>Armand's Properties - <?php print ucwords($type); ?></h2>
  		</div>
        <?php
			if($error == 1){
				?>
                	<div class="alert alert-success">
                      	<strong>Success!</strong> <?php print $error_msg; ?>
                    </div>
                <?php
			}elseif($error == 2){
				?>
                	<div class="alert alert-danger">
                      	<strong>Error!</strong> <?php print $error_msg; ?>
                    </div>
                <?php
			}
		?>
  		<div class="row">
        	<div class="col-md-12">
            	<input type="text" name="address" id="autocomplete" onFocus="geolocate()" class="form-control" autocomplete="off" value="<?php print $geolocate; ?>" />
            	<div id="map"></div>
            </div>
            <div class="col-md-12 order-md-1">
              	<form class="needs-validation" novalidate enctype="multipart/form-data" method="post">
                	<div class="row">
                  		<div class="col-md-6 mb-3">
                    		<label for="town">Town</label>
                    		<input type="text" class="form-control" name="town" id="town" placeholder="" value="<?php print $town; ?>" required>
                    		<div class="invalid-feedback">
                      			Please provide a Town Name
                    		</div>
                  		</div>
                  		<div class="col-md-6 mb-3">
                    		<label for="county">County</label>
                    		<input type="text" class="form-control" name="county" id="county" placeholder="" value="<?php print $county; ?>" required>
                    		<div class="invalid-feedback">
                      			Please provide a County Name
                    		</div>
                  		</div>
                  		<div class="col-md-6 mb-3">
                    		<label for="country">Country</label>
                    		<input type="text" class="form-control" name="country" id="country" placeholder="" value="<?php print $country; ?>" required>
                    		<div class="invalid-feedback">
                      			Please provide a Country Name
                    		</div>
                  		</div>
                  		<div class="col-md-6 mb-3">
                    		<label for="postcode">Postal Code</label>
                    		<input type="text" class="form-control" name="postcode" id="postcode" placeholder="" value="<?php print $postcode; ?>" required>
                    		<div class="invalid-feedback">
                      			Please provide the Postal Code
                    		</div>
                  		</div>
                  		<div class="col-md-6 mb-3">
                    		<label for="displayable_address">Displayable Address</label>
                    		<input type="text" class="form-control" name="displayable_address" id="displayable_address" placeholder="" value="<?php print $displayable_address; ?>" required>
                    		<div class="invalid-feedback">
                      			Please provide a Displayable Address
                    		</div>
                  		</div>
                  		<div class="col-md-6 mb-3">
                    		<label for="coordinates">Coordinates</label>
                    		<input type="text" class="form-control" name="coordinates" id="coordinates" placeholder="" value="<?php print $latitude.", ".$longitude; ?>" required>
                    		<div class="invalid-feedback">
                      			Please provide the Coordinates. If you dont know it, use the Google Address search at the top
                    		</div>
                  		</div>
                  		<div class="col-md-12 mb-3">
                    		<label for="description">Description</label>
                    		<textarea class="form-control" name="description" id="description" required><?php print $description; ?></textarea>
                    		<div class="invalid-feedback">
                      			Please provide a Description
                    		</div>
                  		</div>
                  		<div class="col-md-12 mb-3">
                    		<label for="image">Image</label>
                            <?php
								if($type == "edit"){
									?>
                                    	<input type="file" class="form-control" name="image" id="image" placeholder="" value="">
                                    	<center><img src="<?php print $thumbnail_url; ?>" class="img-thumbnail" alt="Property Thumbnail" /></center>
                                    <?php
								}elseif($type == "add"){
									?>
                                    	<input type="file" class="form-control" name="image" id="image" placeholder="" value="" required>
                                    <?php
								}
							?>
                    		<div class="invalid-feedback">
                      			Please provide a Image
                    		</div>
                  		</div>
                  		<div class="col-md-6 mb-3">
                    		<label for="num_bedrooms">Number of Bedrooms</label>
                    		<input type="number" class="form-control" name="num_bedrooms" id="num_bedrooms" placeholder="" value="<?php print $num_bedrooms; ?>" required>
                    		<div class="invalid-feedback">
                      			Please provide the Number of Bedrooms
                    		</div>
                  		</div>
                  		<div class="col-md-6 mb-3">
                    		<label for="num_bathrooms">Number of Bathrooms</label>
                    		<input type="number" class="form-control" name="num_bathrooms" id="num_bathrooms" placeholder="" value="<?php print $num_bathrooms; ?>" required>
                    		<div class="invalid-feedback">
                      			Please provide the Number of Bathrooms
                    		</div>
                  		</div>
                  		<div class="col-md-6 mb-3">
                    		<label for="price">Price</label>
                    		<input type="number" class="form-control" name="price" id="price" placeholder="" value="<?php print $price; ?>" required>
                    		<div class="invalid-feedback">
                      			Please provide the Price
                    		</div>
                  		</div>
                  		<div class="col-md-6 mb-3">
                    		<label for="property_type">Property Type</label>
                    		<select class="form-control" name="property_type" id="property_type" required>
                            	<option value="">Select...</option>
                                <?php
									$prop = DBC::dbsql("SELECT * FROM property_type ORDER BY title ASC;");
									while($getProp = DBC::dbfetch($prop)){
										$prop_id = $getProp['id'];
										$prop_title = $getProp['title'];
										if($prop_id == $property_type_id){
											?>
                                            	<option value="<?php print $prop_id; ?>" selected="selected"><?php print $prop_title; ?></option>
                                            <?php
										}else{
											?>
                                            	<option value="<?php print $prop_id; ?>"><?php print $prop_title; ?></option>
                                            <?php
										}
									}
								?>
                            </select>
                    		<div class="invalid-feedback">
                      			Please select a property type
                    		</div>
                  		</div>
                        <div class="col-md-12 text-center">
                        	Sale Type
                        </div>
                        <div class="d-block my-3">
                            <div class="custom-control custom-radio">
                                <input id="sale" name="sale_type" value="sale" type="radio" class="custom-control-input" <?php if($sale_type == "sale"){print "checked";} ?> required>
                                <label class="custom-control-label" for="sale">Sale</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input id="rent" name="sale_type" value="rent" type="radio" class="custom-control-input" <?php if($sale_type == "rent"){print "checked";} ?> required>
                                <label class="custom-control-label" for="rent">Rent</label>
                            </div>
                        </div>
                    </div>
                    <hr class="mb-4">
                    <input type="hidden" name="action_type" id="action_type" value="<?php print $type; ?>" />
                    <input type="hidden" name="uuid" id="uuid" value="<?php print $uuid; ?>" />
                	<button class="btn btn-primary btn-lg btn-block" type="submit"><?php print ucwords($type); ?> Property</button>
                    <hr class="mb-4">
                </form>
            </div>
		</div>
	</div>
    
    <script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/form-validation.js"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCSA2ftUGWD7EjFoqykTGYxCme9JtWv8K8&callback=initMap&libraries=places" type="text/javascript"></script>
    <script>
		var geocoder;
		var map;
		var address = '<?php print $geolocate; ?>';
		var action_type = '<?php print $type; ?>';
		
		$(document).ready(function(e) {
			setTimeout("initAutocomplete()", 2000);
		});
		
    	function initMap(){
			geocoder = new google.maps.Geocoder();
			var latlng = new google.maps.LatLng(7.1007414, 20.6134654);
			var myOptions = {
				zoom: 2,
				center: latlng,
				mapTypeControl: true,
				mapTypeControlOptions: {
					style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
				},
				navigationControl: true,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			map = new google.maps.Map(document.getElementById("map"), myOptions);
			if(action_type == "edit"){
				doGeocode(address);
			}
		}
		
		function initAutocomplete(){
			autocomplete = new google.maps.places.Autocomplete((document.getElementById('autocomplete')),{types: ['geocode']});
			
			autocomplete.addListener('place_changed', function() {
				var displayable_address = $('#autocomplete').val();
				var street_number, route, area, administrative_area_level_1, country, postcode;
				var place = autocomplete.getPlace();
				var coordinates = autocomplete.getPlace().geometry.location;
				var address_components_length = place.address_components.length;
				for(var i = 0; i < address_components_length; i++){
					if(place.address_components[i].types[0] == "street_number"){
						street_number = place.address_components[i].long_name;
					}
					if(place.address_components[i].types[0] == "route"){
						route = place.address_components[i].long_name;
					}
					if(place.address_components[i].types[0] == "locality" || place.address_components[i].types[0] == "postal_town"){
						area = place.address_components[i].long_name;
					}
					if(place.address_components[i].types[0] == "administrative_area_level_1"){
						administrative_area_level_1 = place.address_components[i].long_name;
					}
					if(place.address_components[i].types[0] == "country"){
						country = place.address_components[i].long_name;
					}
					if(place.address_components[i].types[0] == "postal_code"){
						postcode = place.address_components[i].long_name;
					}
				}
				$('#town').val(area);
				$('#county').val(administrative_area_level_1);
				$('#country').val(country);
				$('#postcode').val(postcode);
				$('#displayable_address').val(displayable_address);
				$('#coordinates').val(coordinates.lat()+", "+coordinates.lng());
			});
	
		}
		
		function doGeocode(address){
			if(geocoder){
				geocoder.geocode({
					'address': address
				}, function(results, status){
				  if(status == google.maps.GeocoderStatus.OK){
						if(status != google.maps.GeocoderStatus.ZERO_RESULTS){
							map.setCenter(results[0].geometry.location);
							map.setZoom(20);
							var infowindow = new google.maps.InfoWindow({
								content: '<b>' + address + '</b>',
								size: new google.maps.Size(150, 50)
							});
			
							var marker = new google.maps.Marker({
								position: results[0].geometry.location,
								map: map,
								title: address
							});
							google.maps.event.addListener(marker, 'click', function() {
								infowindow.open(map, marker);
							});
						}else{
							alert("No results found");
						}
					}else{
						alert("Unable to find address to display");
					}
				});
			}
		}
    </script>
</body>
</html>
