<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('title') ?>
    <a href="<?= url('patch-panel-port/list')?>">Patch Panel Port</a>
<?php $this->append() ?>

<?php $this->section('page-header-postamble') ?>
    <li>Edit</li>
<?php $this->append() ?>


<?php $this->section('content') ?>
<?php if(session()->has('fail')): ?>
    <div class="alert alert-danger" role="alert">
        <b>Error : </b><?= session()->get('fail') ?>
    </div>
<?php endif; ?>


<?= Former::open()->method('POST')
    ->action(url('patch-panel-port/store'))
    ->customWidthClass('col-sm-3')
    ->addClass('col-md-10');
?>

    <?= Former::text('number')
        ->label('Patch Panel Port Name');
    ?>

    <?= Former::text('patch_panel')
        ->label('Patch Panel')
    ?>

    <?= Former::checkbox('duplex')?>

    <span id='duplex-port-area' style="display: none">
        <?= Former::select('partner_port')
            ->label('Partner Port')
            ->fromQuery($t->partnerPorts, 'name')
            ->placeholder('Choose a partner port')
            ->addClass('chzn-select')
        ?>
    </span>

    <div class="well">
        <?= Former::default_button()
            ->addClass('reset-button-well')
            ->icon('glyphicon glyphicon-refresh')
            ->title('Reset')
            ->style('margin-top : 1%')
            ->id('resetSwitchSelect');
        ?>

        <?= Former::select('switch')
            ->label('Switch')
            ->fromQuery($t->switches, 'name')
            ->placeholder('Choose a switch')
            ->addClass('chzn-select')
        ?>

        <?= Former::select('switch_port')
            ->label('Switch Port')
            ->fromQuery($t->switchPorts, 'name')
            ->placeholder('Choose a switch port')
            ->addClass('chzn-select')
        ?>
    </div>

    <div class="well">
        <?= Former::default_button()
            ->addClass('reset-button-well')
            ->icon('glyphicon glyphicon-refresh')
            ->title('Reset')
            ->id('resetCustomer');
        ?>

        <?= Former::select('customer')
            ->label('Customer')
            ->fromQuery($t->customers, 'name')
            ->placeholder('Choose a customer')
            ->addClass('chzn-select')
        ?>
    </div>

    <?= Former::select('state')
        ->label('States')
        ->options($t->states)
        ->placeholder('Choose a states')
        ->addClass('chzn-select')
    ?>

    <?= Former::textarea('notes')
        ->label('Note')
    ?>

    <?= Former::date('assigned_at')
        ->label('Assigned At')
        ->append('<button class="btn-default btn" onclick="setToday(\'assigned_at\')" type="button">Today</button>')
    ?>

    <?= Former::date('connected_at')
        ->label('Connected At')
        ->append('<button class="btn-default btn" onclick="setToday(\'connected_at\')" type="button">Today</button>')
    ?>

    <?= Former::date('ceased_requested_at')
        ->label('Ceased Requested At')
        ->append('<button class="btn-default btn" onclick="setToday(\'ceased_requested_at\')" type="button">Today</button>')
    ?>

    <?= Former::date('ceased_at')
        ->label('Ceased Requested At')
        ->append('<button class="btn-default btn" onclick="setToday(\'ceased_at\')" type="button"">Today</button>')
    ?>

    <?= Former::text('last_state_change_at')
        ->label('Last State change At')
    ?>

    <?= Former::radios('chargeable')
        ->radios(array(
            'Yes' => array('chargeable' => 'yes', 'value' => '1'),
            'No' => array('chargeable' => 'no', 'value' => '0'),
        ))->inline()->check($t->patchPanelPort->getChargeableInt())?>

    <?= Former::radios('internal_use')
        ->radios(array(
            'Yes' => array('name' => 'internal_use', 'value' => '1'),
            'No' => array('name' => 'internal_use', 'value' => '0'),
        ))->inline()->check($t->patchPanelPort->getInternalUseInt())?>

    <?= Former::hidden('patch_panel_port_id')
        ->value($t->patchPanelPort->getId())
    ?>

    <?= Former::hidden('switch_port_id')
        ->id('switch_port_id')
        ->value($t->patchPanelPort->getSwitchPortId())
    ?>

    <?=Former::actions( Former::primary_submit('Save Changes'),
        Former::default_button('Cancel')
    );?>

    <?= Former::hidden('date')
        ->id('date')
        ->value(date('Y-m-d'))
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->patchPanelPort ? $t->patchPanelPort->getId() : '' )
    ?>

<?= Former::close() ?>


<?php $this->append() ?>


<?php $this->section('scripts') ?>
    <script>
        function setToday(inputName){
            $("#"+inputName).val($("#date").val());
        }

        $(document).ready(function() {


            if($('#switch_port').val() != null){
                setCustomer();
            }



            $('#duplex').change(function(){
                if(this.checked){
                    $("#duplex-port-area").show();
                }
                else{
                    $("#duplex-port-area").hide();
                }
            });



            if(<?= (int)$t->hasDuplex ?> ){
                $('#duplex').click();
            }

            $("#number").prop('readonly', true);
            $("#patch_panel").prop('readonly', true);
            $("#last_state_change_at").prop('readonly', true);

            $("#switch").change(function(){
                setSwitchPort();
            });

            $("#switch_port").change(function(){
                setCustomer();
            });

            function setSwitchPort(){
                $("#switch_port").html("<option value=\"\">Loading please wait</option>\n");
                $("#switch_port").trigger("chosen:updated");
                switchId = $("#switch").val();
                customerId = $("#customer").val();
                switchPortId = $("#switch_port_id").val();
                $.ajax({
                    url: "<?= url('patch-panel-port/getSwitchPort/')?>",
                    data: {switchId: switchId, customerId: customerId, switchPortId : switchPortId},
                    type: 'GET',
                    dataType: 'JSON',
                    success: function (data) {
                        if(data.success){
                            var options = "<option value=\"\">Choose a switch port</option>\n";
                            $.each(data.response,function(key, value){
                                options += "<option value=\"" + value.id + "\">" + value.name + " (" + value.type + ")</option>\n";
                            });
                            $("#switch_port").html(options);
                            $("#switch_port").trigger("chosen:updated");
                        }
                    }
                });
            }

            function setCustomer(){
                if($("#switch").val() != ''){
                    switchPortId = $("#switch_port").val();
                    $("#customer").html("<option value=\"\">Loading please wait</option>\n");
                    $("#customer").trigger("chosen:updated");
                    $.ajax({
                        url: "<?= url('patch-panel-port/getCustomerForASwitchPort/')?>",
                        data: {switchPortId: switchPortId},
                        type: 'GET',
                        dataType: 'JSON',
                        success: function (data) {
                            if(data.success){
                                $("#customer").html("<option value=\"" + data.response.id + "\">" + data.response.name + "</option>\n");
                                $("#customer").trigger("chosen:updated");
                            }
                            else{
                                $("#customer").html("");
                                $("#customer").trigger("chosen:updated");
                            }
                        }

                    });
                }
            }

            $("#customer").change(function(){
                    $("#switch").html("<option value=\"\">Loading please wait</option>\n");
                    $("#switch").trigger("chosen:updated");
                    $("#switch_port").html("");
                    $("#switch_port").trigger("chosen:updated");
                    customerId = $("#customer").val();
                    $.ajax({
                        url: "<?= url('patch-panel-port/getSwitchForACustomer/')?>",
                        data: {customerId: customerId},
                        type: 'GET',
                        dataType: 'JSON',
                        success: function (data) {
                            if(data.success){
                                var options = "<option value=\"\">Choose a switch</option>\n";
                                $.each(data.response,function(key, value){
                                    options += "<option value=\"" + key + "\">" + value + "</option>\n";
                                });
                                $("#switch").html(options);
                                $("#switch").trigger("chosen:updated");
                            }
                            else{
                                $("#switch").html("");
                                $("#switch").trigger("chosen:updated");
                            }
                        }

                    });
            });

            $("#resetCustomer").click(function(){

                    options = "<option value=''> Choose a customer</option>\n";
                    <?php foreach ($t->customers as $id => $customer): ?>
                        customer = '<?= $customer ?>';
                        options += "<option value=\"" + <?= $id ?> + "\">" + customer  + "</option>\n";
                    <?php endforeach; ?>
                    $("#customer").html(options);
                    $("#customer").trigger("chosen:updated");


            });

            $("#resetSwitchSelect").click(function(){
                if($("#switch").val() != null && $("#switch_port").val() != null){
                    options = "<option value=''> Select a customer</option>\n";
                    <?php foreach ($t->switches as $id => $switch): ?>
                        $switch = '<?= $switch ?>';
                        options += "<option value=\"" + <?= $id ?> + "\">" + $switch  + "</option>\n";
                    <?php endforeach; ?>
                    $("#switch").html(options);
                    $("#switch").trigger("chosen:updated");
                    $("#switch_port").html('');
                    $("#switch_port").trigger("chosen:updated");
                }

            });
        });
    </script>
<?php $this->append() ?>