{capture name=path}{l s='Part specs'}{/capture}
<header class="well">
	<div class="col-sm-2">
		<img src="//empresasnoffra.com/img/catalogo/{$referencia}.jpg" alt="{$producto.Description}"  class="img-responsive">
	</div>
	<div class="col-sm-10">
		<h1 class="page-heading">{l s='Part specs'}: {$referencia}</h1>
		<h3>{$descripcion}</h3>
	</div>
</header>
{include file="$tpl_dir./errors.tpl"}
<!-- CODIGO DE LA VISTA -->
<section id="partfinder-spec-results">
	<div class="row">
		<div class="col-xs-12">
			<h4>{l s="Part specs & variations for"}: {$referencia}</h4>
			{if $fichastecnicas != null && $is_starter_or_alternator == true}
				<div class="table-container">
					<table class="table table-striped table-condensed">
						{for $i = 0 to $cf_fichatecnica}
							<tr>
								{for $j = 0 to $cc_fichatecnica}
									{if $i == 0 && $j == 0}
										<th> </th>
									{elseif $i == 0 || $j == 0}
										<th class="info">{$fichastecnicas[$i][$j]}</th>
									{else}
										<td>{$fichastecnicas[$i][$j]}</td>
									{/if}
								{/for}
							</tr>
						{/for}
					</table>
				</div>
			{/if}
		</div><!-- .col-xs-9 -->
	</div><!-- .row -->
</section>