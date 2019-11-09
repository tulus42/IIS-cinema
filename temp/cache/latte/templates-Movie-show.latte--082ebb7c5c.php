<?php
// source: /opt/lampp/htdocs/IIS-cinema/app/MovieModule/templates/Movie/show.latte

use Latte\Runtime as LR;

class Template082ebb7c5c extends Latte\Runtime\Template
{
	public $blocks = [
		'content' => 'blockContent',
	];

	public $blockTypes = [
		'content' => 'html',
	];


	function main()
	{
		extract($this->params);
		if ($this->getParentName()) return get_defined_vars();
		$this->renderBlock('content', get_defined_vars());
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
?>
    <div class="column8">
        <div class="one-movie-container">
            <div class="row">
                <h1 class="one-movie-title"><?php echo LR\Filters::escapeHtmlText($piece_of_work->name) /* line 5 */ ?></h1>
            <div class="row">

            <div class="row">
                <div class="column3">
                    <img class="one-movie-image" src=<?php echo LR\Filters::escapeHtmlAttrUnquoted(LR\Filters::safeUrl($piece_of_work->picture)) /* line 10 */ ?>>
                </div>

                <div class="column5">
                    <div class="row">
                        <h2>Žáner: <?php echo LR\Filters::escapeHtmlText($piece_of_work->genre) /* line 15 */ ?></h2>
                    </div>

                    <div class="row">
                        <h2>Dĺžka: <?php echo LR\Filters::escapeHtmlText($piece_of_work->duration) /* line 19 */ ?> min.</h2>
                    </div>

                    <div class="row">
                        <h2>Typ: <?php echo LR\Filters::escapeHtmlText($piece_of_work->type) /* line 23 */ ?></h2>
                    </div>

                    <div class="row">
                        <h2>Hodnotenie: <?php echo LR\Filters::escapeHtmlText($piece_of_work->rating) /* line 27 */ ?>%</h2>
                    </div>

                    <div class="row">
                        <h2>Účinkujú:</h2>
                    </div>                    
                </div>
            </div>

            <div class="row">
                <div class="one-movie-description">
                    <h2>Popis:</h2>
                </div>
            </div>
            <div class="row">
                <div class="one-movie-description">
                    <p><?php echo LR\Filters::escapeHtmlText($piece_of_work->description) /* line 43 */ ?></p>
                <div>
            </div>
            
            <div class="row">
                <button class="button event">
                    Pridať<br>predstavenie
                </button>
            </div>
        </div>
	</div>
<?php
	}

}
