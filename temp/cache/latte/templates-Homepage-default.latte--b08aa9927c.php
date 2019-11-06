<?php
// source: /opt/lampp/htdocs/IIS-cinema/app/CoreModule/templates/Homepage/default.latte

use Latte\Runtime as LR;

class Templateb08aa9927c extends Latte\Runtime\Template
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
		if (!$this->getReferringTemplate() || $this->getReferenceType() === "extends") {
			if (isset($this->params['movie'])) trigger_error('Variable $movie overwritten in foreach on line 6');
		}
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
?>
	<div class="row">
        <h1 class="cinema-title">Práve v predaji</h1>
    </div>

<?php
		$iterations = 0;
		foreach ($movies as $movie) {
?>
	<div class="column4">
		<div class="movie-container">
			<p class="movie-title"><a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Movie:show", [$movie->id_piece_of_work])) ?>" style="color:#3f4547"><?php
			echo LR\Filters::escapeHtmlText($movie->name) /* line 9 */ ?></a></p>
		
			<div class="movie-poster">
				<a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Movie:show", [$movie->id_piece_of_work])) ?>"><img src=<?php
			echo LR\Filters::escapeHtmlAttrUnquoted(LR\Filters::safeUrl($movie->picture)) /* line 12 */ ?>></a>
			</div>

			<div class="movie-description">
				<p><?php echo LR\Filters::escapeHtmlText(($this->filters->truncate)($movie->description, 370, '...')) /* line 16 */ ?></p>
			</div>

			
			<div class="buy-tickets">
				<button class="buy-button" type="button">Objednať lístky</button>
			</div>
		</div>
	</div>
<?php
			$iterations++;
		}
		
	}

}
