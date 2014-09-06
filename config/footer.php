<!-- footer -->


<?php
if ($demo_mode <> '0') {
  print '<hr><h3>* Demo Site - Admin Pages Are Read-Only  *</h3>';
}
?>


	</div>
</div>


<footer id="footer" class="mvl clearfix">
	<small class="pvl">
		<a href="https://github.com/daigofuji/softballstats"><b>SoftballStats <?php echo $version ?></b></a>
 
 		Written by: <a href="mailto:dev@swillers.com">David Carlo</a> (<a href="http://softballstats.sourceforge.net/">original</a>). Edited by <a href="https://github.com/daigofuji/">Daigo Fujiwara</a> 
	</small>
</footer>

<!-- Javascript -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/foundation/5.2.3/js/foundation.min.js"></script>
<script src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/725b2a2115b/integration/foundation/dataTables.foundation.js"></script>

<script>


$(document).foundation().ready(function(){
	"use strict";

	$('.stats').dataTable( {
		"paging": false,
		"searching": false
	});

});


</script>

<!-- Google Analytics -->
<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	ga('create', 'UA-17687426-1', 'auto');
	ga('send', 'pageview');
</script>

</body>
</html>
