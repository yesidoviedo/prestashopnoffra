{capture name=path}{l s='Advanced part finder'}{/capture}

<h1 class="page-heading">{l s='Search results for:'} '{$reference}'</h1>

{include file="$tpl_dir./errors.tpl"}

<!-- CODIGO DE LA VISTA -->



{if $products == null}

	<h3 class="text-center bg-warning text-danger">{l s="No results for your"}</h3>

{else}

	<div class="row">

		<div class="col-md-12">

			<table class="table table-hover table-condensed tablesorter" id="part-finder-datatable">

				<thead>

					<tr>

						<th class="info"></th>

						<th class="info">{l s="QPS code"}</th>

						<th class="info">{l s="Equivalent code"}</th>

						<th class="info">{l s="Product"}</th>

						<th class="info">{l s="Line"}</th>

						<th class="info"></th>

					</tr>

				</thead>

				<tbody>

					{foreach $products as $product}

						<tr>

							<td width="110">

                                {if $product.active}

                                    <a href="index.php?controller=product&id_product={$product.id_product}&search_query={$reference}">

                                        <img src="//empresasnoffra.com/img/catalogo/{$product.reference}.jpg" alt="{$product.name}" class="img-responsive">

                                    </a>

                                {else}

                                    <img src="//empresasnoffra.com/img/catalogo/{$product.reference}.jpg" alt="{$product.name}" class="img-responsive">

                                {/if}

							</td>

							<td>

								{if $product.active}

                                    <a href="index.php?controller=product&id_product={$product.id_product}&search_query={$reference}" class="product-size">

                                        {$product.reference}

                                    </a>

                                {else}

                                    {$product.reference}

                                {/if}

							</td>

							<td>

								{if $product.active}

                                    <a href="index.php?controller=product&id_product={$product.id_product}&search_query={$reference}" class="product-size">

                                        {$reference}

                                    </a>

                                {else}

                                    {$reference}

                                {/if}

							</td>

							<td>

								{if $product.active}

                                    <a href="index.php?controller=product&id_product={$product.id_product}&search_query={$reference}" class="product-size">

										<span class="product-name">{$product.name}</span>

										{if $logged}

											{if ! isset($product.specific_price)}

												<span class="product-price">{convertPrice price=$product.price|floatval}</span>

											{else}

												<span class="product-price">{convertPrice price=$product.specific_price|floatval}</span>

											{/if}

  										{/if}

                                    </a>

                                {else}

                                    {$product.name}

                                    <span class="badge badge-danger" style="background: #d50000;">{l s='Not available'}</span>

                                {/if}

							</td>

							<td>

								{if $product.active}

                                    <a href="index.php?controller=product&id_product={$product.id_product}&search_query={$reference}" class="product-size">

                                        {$product.line}

                                    </a>

                                {else}

                                    {$product.line}

                                {/if}

							</td>

							<td>

                                {if $product.active}

                                    <a href="index.php?controller=product&id_product={$product.id_product}&search_query={$reference} "class="btn btn-sm btn-block btn-success">

                                        {l s="Buy this"}

                                    </a>

                                {/if}

							</td>

						</tr>

					{/foreach}

				</tbody>

			</table>

		</div>

	</div>

{/if}



<section class="bg-info">

    <div class="col-xs-1"><i class="icon icon-4x icon-search-more"></i></div>

    <div class="col-xs-11 alpha">

        <h3>{l s="Can't find what you need?"}</h3>

        <p>{l s="Tell us what you want, if we didn't have it, we will find it for you:"} <a href="{$link->getPageLink('contact', true)}" class="btn btn-primary">{l s="Contact Us"}</a></p>

    </div>

</section>



{if $lang_iso == es}

	{literal}

		<script>

		$(document).ready(function() 

			{ 

				$("#part-finder-datatable").DataTable({

					"lengthMenu": [ [25, 50, 100, -1], [25, 50, 100, "All"] ],

					"language": {

						"search":"Buscar:",

						"lengthMenu": "_MENU_ registros por pagina",

						"zeroRecords": "No encontramos lo que buscas.",

						"info": "Pagina _PAGE_ de _PAGES_",

						"infoEmpty": "No hay registros disponibles",

						"infoFiltered": "(filtrado de _MAX_ registros en total)"

					},

					"order": []

				});

			} 

		); 

		</script>

	{/literal}

{else}

	{literal}

		<script>

		$(document).ready(function() 

			{ 

				$("#part-finder-datatable").DataTable({

					"lengthMenu": [ [25, 50, 100, -1], [25, 50, 100, "All"] ],

					"language": {

						"lengthMenu": "_MENU_ records per page",

						"zeroRecords": "Nothing found - sorry",

						"info": "Showing page _PAGE_ of _PAGES_",

						"infoEmpty": "No records available",

						"infoFiltered": "(filtered from _MAX_ total records)"

					},

					"order": []

				});

			} 

		); 

		</script>

	{/literal}

{/if}