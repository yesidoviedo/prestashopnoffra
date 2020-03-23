<div class="container">
    <form action="{$link->getPageLink('partfinder', true)}" method="post" id="form_productos">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#partNumber" aria-controls="partNumber" role="tab" data-toggle="tab" id="partNumberTab">{l s="Search by Part No.:"}</a>
            </li>
            <li role="presentation">
                <a href="#browsePart" aria-controls="browsePart" role="tab" data-toggle="tab" id="browsePartTab">{l s="Browse part by vehicle:"}</a></li>
            <li role="presentation">
                
            </li>  
        </ul>
        <!-- Tab panes -->
        <div class="row">
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="partNumber">
                    <div class="col-sm-10">
                        <div class="form-inline">
                            <div class="form-group">
                                <input class="form-control" name="codigo" id="codigo" placeholder="Part number">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <button type="submit" name="enviar" id="enviarCodigo" class="btn btn-block btn-primary btn-sm" disabled="">Search</button>
                    </div>
                </div><!-- .tab-pane -->
                <div role="tabpanel" class="tab-pane" id="browsePart">
                    <div class="col-sm-10">
                        <div class="form-inline">
                            <div class="form-group">
                                <select class="form-control" name="marca" id="marca" disabled>
                                    <option value="" disabled selected>{l s="Make"}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="modelo" id="modelo" disabled>
                                    <option value="" disabled selected>{l s="Model"}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="anho" id="anho" disabled>
                                    <option value="" disabled selected>{l s="Year"}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="repuesto" id="repuesto" disabled>
                                    <option value="" disabled selected>{l s="Part type"}</option>
                                </select>
                            </div>
                        </div><!-- .form-inline -->
                    </div>
                    <div class="col-sm-2">
                        <button type="submit" name="enviar" id="enviar" class="btn btn-block btn-primary btn-sm" disabled="">Search</button>
                    </div>
                </div><!-- .tab-pane -->
            </div><!-- .tab-content -->
        </div><!-- .row -->
    </form>
</div><!--.container-fluid -->