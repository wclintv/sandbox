<!DOCTYPE html>
<html>
	<head>
		<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script> -->
		<script type="text/javascript" src="https://appcenter.intuit.com/Content/IA/intuit.ipp.anywhere.js"></script>
		<script>
			var url = '{{ $grant_url }}';
	        //intuit anywhere setup            
	        intuit.ipp.anywhere.setup({
	        	menuProxy: '',
	        	grantUrl: url           	  
	        });
	        intuit.ipp.anywhere.directConnectToIntuit(); 
	    </script>
	</head>
	<body>
	</body>
</html>
