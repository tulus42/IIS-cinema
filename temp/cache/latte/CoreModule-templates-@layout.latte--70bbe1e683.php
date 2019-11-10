<?php
// source: /opt/lampp/htdocs/IIS-cinema/app/CoreModule/templates/@layout.latte

use Latte\Runtime as LR;

class Template70bbe1e683 extends Latte\Runtime\Template
{
	public $blocks = [
		'scripts' => 'blockScripts',
	];

	public $blockTypes = [
		'scripts' => 'html',
	];


	function main()
	{
		extract($this->params);
?>
 
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">

	<title>Your Cinema</title>

	<!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- CSS files -->
    <link rel="stylesheet" type="text/css" href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 14 */ ?>/css/basic_style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 15 */ ?>/css/1140.css">
	<link rel="stylesheet" type="text/css" href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 16 */ ?>/css/one_movie.css">

	<script src="https://kit.fontawesome.com/b99e675b6e.js"></script>
</head>

<body>
<?php
		$iterations = 0;
		foreach ($flashes as $flash) {
			?>	<div<?php if ($_tmp = array_filter(['flash', $flash->type])) echo ' class="', LR\Filters::escapeHtmlAttr(implode(" ", array_unique($_tmp))), '"' ?>><?php
			echo LR\Filters::escapeHtmlText($flash->message) /* line 22 */ ?></div>
<?php
			$iterations++;
		}
?>

	<div class="wrapper">
        <div class="sidebar">
			<div class="wrap">
				<div class="search">
					<input type="text" class="searchTerm" placeholder="Hľadám...">
					<button type="submit" class="searchButton">
						<i class="fa fa-search"></i>
					</button>
				</div>
			</div>    

        
            <ul>
				<li><a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Homepage:default")) ?>"><i class="fas fa-home"></i>Domov</a></li>
				<li><a href="#"><i class="fas fa-film"></i>Filmy</a></li>
				<li><a href="#"><i class="fas fa-chair"></i>Sály</a></li>
				<li><a href="#"><i class="fas fa-address-card"></i>O nás</a></li>
				<li><a href="#"><i class="fas fa-address-book"></i>Kontakt</a></li>
            </ul> 
        </div>
    </div>

	<div class="main_content">
            <!-- HEADER -->
            <header class="head">
                <div class="row">
                    <div class="column4" id="header-logo">
                        <a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Homepage:default")) ?>"><img src="<?php
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 51 */ ?>/img/final_logo.png">
                    </div>                

                    <div class="column8">
                        <ul>
                            <li class="log-link link"><a href="{{ route('login') }}">Prihlásenie</a></li>
                            <li class="reg-link link"><a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Registration:register")) ?>">Registrácia</a></li>
                                    
                        </ul>
                    </div>
                </div>
            </header>

            <hr class="new">
            

<?php
		$this->renderBlock('content', $this->params, 'html');
?>

<?php
		if ($this->getParentName()) return get_defined_vars();
		$this->renderBlock('scripts', get_defined_vars());
?>
</body>
</html><?php
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		if (!$this->getReferringTemplate() || $this->getReferenceType() === "extends") {
			if (isset($this->params['flash'])) trigger_error('Variable $flash overwritten in foreach on line 22');
		}
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockScripts($_args)
	{
?>	<script src="https://nette.github.io/resources/js/3/netteForms.min.js"></script>
<?php
	}

}
