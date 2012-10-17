<?php defined('_JEXEC') or die; 

define('DS', DIRECTORY_SEPARATOR);

require_once(dirname(__FILE__).DS.'php'.DS.'j3.php');

$j3 = new j3();


?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<jdoc:include type="head" />
	
	</head>
	<body>
		<div class="container">
			<div class="header">
				<div class="top">
					<jdoc:include type="modules" name="top" style="none" />
				</div>
				<div class="navbar">
					<div class="navbar-inner">
						<div class="container">
							<jdoc:include type="modules" name="mainmenu" style="none"/>
							
						</div>
					</div>
				</div>
				<div>
					<jdoc:include type="modules" name="breadcrumb" style="none"/>
				</div>
			</div>
			<div class="row">
				<!-- Begin Content -->
				<div class="span3">
					<jdoc:include type="modules" name="left" style="default"/>
				</div>
				
				<div class="span6">
					<jdoc:include type="message" />
					<jdoc:include type="component" />
				</div>
				
				<div class="span3">
					<jdoc:include type="modules" name="right" style="default"/>
				</div>
				<!-- End Content -->
			</div>
			<div class="footer">
				<jdoc:include type="modules" name="footer" style="default"/>
			</div>
		</div>
	
	</body>
</html>

