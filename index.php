<?php 
    
    class Data { 

        function Data($file) { 
            
            // Loads a data file and prepares it for usage
            $this->doc = new DOMDocument();
            $this->doc->load($file);
            $this->xp = new DOMXPath($this->doc);
            $this->nodes = $this->xp->query('/data/page');
            $this->node = $this->xp->query('/data/page[@href="' . $this->value() . '"]')->item(0);
        }
        
        function state() {
            
            // Returns the base state
            return substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));
        }
        
        function value() {
            
            // Returns the state value
            return str_replace($this->state(), '', $_SERVER['REQUEST_URI']);
        }
        
        function nav() {
            $str = '';
            
            // Prepares the navigation links
            foreach ($this->nodes as $node) {
                $href = $node->getAttribute('href');
                $title = $node->getAttribute('title');
                $str .= '<li><a href="' . $this->state() . $href . '"' 
                    . ($this->value() == $href ? ' class="selected"' : '') . '>' . $title . '</a></li>';
            }
            return trim($str);
        }
        
        function content() {
            $str = '';
            
            // Prepares the content
            if (isset($this->node)) {
                foreach ($this->node->childNodes as $node) {
                    $str .= $this->doc->saveXML($node);
                }
            } else {
                $str .= '<p>Page not found.</p>';
            }
            
            return trim($str);
        }
        
        function title(){
            $str='';
            
            // Prepares the title
            foreach($this->nodes as $node){
                $href=$node->getAttribute('href');
                $title=$node->getAttribute('title');
                $str.=($this->value()==$href?$title:'');
            }
            return trim($str);
        }
    }
    
    $data = new Data('xml/data.xml');

?>
<!doctype html>
<html lang="en">
	
	<head> <title>S O S U P E R S A M - <?php echo($data->title()); ?></title> 
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <link type="text/css" href="css/styles.css" rel="stylesheet">
        <script type="text/javascript" src="js/jquery-1.7.2.js"></script>
        <script type="text/javascript" src="js/jquery.address-1.4.min.js"></script>

        <script type="text/javascript" src="http://use.typekit.com/onv3jgn.js"></script>
<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
        <script type="text/javascript">

            $.address.state('<?php echo($data->state()); ?>').init(function() {

                // Initializes the plugin
                $('.nav a').address();
                
            }).change(function(event) {

                // Selects the proper navigation link
                $('.nav a').each(function() {
                    if ($(this).attr('href') == ($.address.state() + event.path)) {
                        $(this).addClass('selected').focus();
                    } else {
                        $(this).removeClass('selected');
                    }
                });

                // Handles response
                var handler = function(data) {
                    $('title').html($('title', data).html());
                    $('.content').html($('.content', data).html());
                    $('.page').show();
                    $.address.title(/>([^<]*)<\/title/.exec(data)[1]);
                };

                // Loads the page content and inserts it into the content area
                $.ajax({
                    url: $.address.state() + event.path,
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        handler(XMLHttpRequest.responseText);
                    },
                    success: function(data, textStatus, XMLHttpRequest) {
                        handler(data);
                    }
                });
            });

            // Hides the tabs during initialization
            document.write('<style type="text/css"> .page { display: none; } </style>');
            
        </script> 
<script src="js/init.js"></script>
<script>
  
</script>

		<script type="text/javascript">




			function init() {
			
			}

		</script>

 


	</head>

	<body>

            <nav>
            <ul class="nav"><?php echo($data->nav()); ?></ul>
        </nav><!-- nav -->

		    <div class="page"> 
            <div class="content"><?php echo($data->content()); ?></div>
        </div>
        
		<div id="logo"><a href="/"><img src="images/logo-small.png" /></a></div>
		
		
	</body>

</html>