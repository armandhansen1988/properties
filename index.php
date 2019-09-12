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
  		<div class="py-2 text-center">
    		<h2>Armand's Properties</h2>
  		</div>
  		<div class="row">
            <div class="col-md-12 mt-1" align="center">
                Show <select name="page_size" id="page_size"><option value="20">20</option><option value="50">50</option><option value="100">100</option></select> entries
            </div>
            <div class="col-md-12 mt-1" align="center">
            	<button type="button" class="btn btn-primary" style="width: 200px;" onclick="loadProperties();">Load Properties</button>
            </div>
            <div class="col-md-12 mt-1" align="center">
            	<button type="button" class="btn btn-primary" style="width: 200px;" onclick="window.location='property-add-1'">Add Property</button>
            </div>
		</div>
        <div class="row">
        	<div class="col-md-12" id="results"></div>
        </div>
        <div class="modal" id="confirmDelete">
          	<div class="modal-dialog">
            	<div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Confirm...</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this property from the listings?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="confirmDelete();">Confirm</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <input type="hidden" id="delete_uuid" value="" />
                    </div>
                </div>
          	</div>
        </div>
	</div>
    
    <script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/form-validation.js"></script>
    <script type="text/javascript" src="js/java.js"></script>
</body>
</html>