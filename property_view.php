<?php
	require_once("includes/init.php");
	require_once("includes/DBC.php");
	
	$uuid 						= DBC::dbescape($_GET['uuid']);
	$property 					= DBC::dbsql("SELECT * FROM properties WHERE uuid = '$uuid';");
	$getProperty 				= DBC::dbfetch($property);
	$county 					= $getProperty['county'];
	$country 					= $getProperty['country'];
	$town 						= $getProperty['town'];
	$description 				= $getProperty['description'];
	$image_url 					= $getProperty['image_url'];
	$latitude 					= $getProperty['latitude'];
	$longitude 					= $getProperty['longitude'];
	$num_bedrooms 				= $getProperty['num_bedrooms'];
	$num_bathrooms 				= $getProperty['num_bathrooms'];
	$price 						= $getProperty['price'];
	$property_type_id 			= $getProperty['property_type_id'];
	$sale_type 					= $getProperty['sale_type'];
	
	$proptype 					= DBC::dbsql("SELECT * FROM property_type WHERE id = '$property_type_id';");
	$getPropType 				= DBC::dbfetch($proptype);
	$property_type_title 		= $getPropType['title'];
	$property_type_description 	= $getPropType['description'];
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
    		<h2>Armand's Properties</h2>
  		</div>
  		<div class="row">
            <div class="col-md-12">
            	<div id="map"></div>
            </div>
            <div class="col-md-12 mt-2">
            	<table class="table table-striped small">
                	<tr>
                        <td colspan="2" align="center"><img src="<?php print $image_url; ?>" width="100%" alt="Property" /></td>
                    </tr>
                	<tr>
                    	<th scope="col">Price</th>
                        <td><?php print number_format($price, 0)." GBP"; ?></td>
                    </tr>
                	<tr>
                    	<th scope="col">Town</th>
                        <td><?php print $town; ?></td>
                    </tr>
                	<tr>
                    	<th scope="col">County</th>
                        <td><?php print $county; ?></td>
                    </tr>
                	<tr>
                    	<th scope="col">Country</th>
                        <td><?php print $country; ?></td>
                    </tr>
                	<tr>
                    	<th scope="col">Description</th>
                        <td><?php print $description; ?></td>
                    </tr>
                	<tr>
                    	<th scope="col">Number of Bedrooms</th>
                        <td><?php print $num_bedrooms; ?></td>
                    </tr>
                	<tr>
                    	<th scope="col">Number of Bathrooms</th>
                        <td><?php print $num_bathrooms; ?></td>
                    </tr>
                	<tr>
                    	<th scope="col">Property Type</th>
                        <td><?php print $property_type_title; ?></td>
                    </tr>
                	<tr>
                    	<th scope="col">Property Description</th>
                        <td><?php print $property_type_description; ?></td>
                    </tr>
                	<tr>
                    	<th scope="col">Coordinates</th>
                        <td><?php print $latitude.", ".$longitude; ?></td>
                    </tr>
                </table>
            </div>
		</div>
	</div>
    
    <script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/form-validation.js"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCSA2ftUGWD7EjFoqykTGYxCme9JtWv8K8&callback=initMap" type="text/javascript"></script>
    <script>
		var map;
		
    	function initMap(){
			var latlng = new google.maps.LatLng(<?php print $latitude; ?>, <?php print $longitude; ?>);
			var myOptions = {
				zoom: 20,
				center: latlng,
				mapTypeControl: true,
				mapTypeControlOptions: {
					style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
				},
				navigationControl: true,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			map = new google.maps.Map(document.getElementById("map"), myOptions);
			var marker = new google.maps.Marker({
				position: latlng,
				map: map
			});
		}
    </script>
</body>
</html>