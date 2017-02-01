<?php

/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */


namespace IXP\Http\Controllers\PatchPanel;

use D2EM;
Use DateTime;

use Entities\Customer;
use Entities\PatchPanelPort;
use Entities\Switcher;
use Entities\SwitchPort;

use Former\Facades\Former;

use IXP\Http\Controllers\Controller;
use IXP\Http\Requests\StorePatchPanelPort;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

use Illuminate\View\View;



/**
 * PatchPanelPort Controller
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   PatchPanel
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */


class PatchPanelPortController extends Controller
{
    /**
     * Display all the patch panel ports
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     * @parama  int $id allow to display all the port for a patch panel => if null display all ports for all patch panel
     * @return  view
     */
    public function index(int $id = null): View{
        return view('patch-panel-port/index')->with([
            'patchPanelPorts'    => D2EM::getRepository(PatchPanelPort::class)->getAllPatchPanelPort($id)
        ]);
    }

    /**
     * Display the form to edit a patch panel port
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     * @params  $request instance of the current HTTP request
     * @parama  int $id patch panel port that need to be edited
     * @return  view
     */
    public function edit(Request $request, int $id = null) {
        $patchPanelPort = false;

        if($id != null){
            if( !( $patchPanelPort = D2EM::getRepository( PatchPanelPort::class )->find($id) ) ) {
                abort(404);
            }

            // display master port informations
            if($patchPanelPort->getDuplexMasterPort() != null){
                $patchPanelPort = $patchPanelPort->getDuplexMasterPort();
            }

            $hasDuplex = $patchPanelPort->hasSlavePort();

            Former::populate( array('number'                => $patchPanelPort->getNumber(),
                                    'patch_panel'           => $patchPanelPort->getPatchPanel()->getName(),
                                    'switch'                => $patchPanelPort->getSwitchId(),
                                    'switch_port'           => $patchPanelPort->getSwitchPortId(),
                                    'customer'              => $patchPanelPort->getCustomerId(),
                                    'partner_port'          => $patchPanelPort->getDuplexSlavePortId(),
                                    'state'                 => $patchPanelPort->getState(),
                                    'notes'                 => $patchPanelPort->getNotes(),
                                    'assigned_at'           => $patchPanelPort->getAssignedAtFormated(),
                                    'connected_at'          => $patchPanelPort->getConnectedAtFormated(),
                                    'ceased_requested_at'   => $patchPanelPort->getCeaseRequestedAtFormated(),
                                    'ceased_at'             => $patchPanelPort->getCeasedAtFormated(),
                                    'last_state_change_at'  => $patchPanelPort->getLastStateChangeFormated(),
            ));

            if($hasDuplex){
                $partnerPorts =  array($patchPanelPort->getDuplexSlavePortId() => $patchPanelPort->getDuplexSlavePortName());
            }
            else{
                $partnerPorts = D2EM::getRepository(PatchPanelPort::class)->getPatchPanelPortAvailableForDuplex($patchPanelPort->getPatchPanel()->getId(), $patchPanelPort->getId());
            }

        }
        else{
            return Redirect::to('patch-panel-port/list');
        }

        return view('patch-panel-port/edit')->with([
                'states'            => \Entities\PatchPanelPort::$STATES,
                'customers'         => D2EM::getRepository(Customer::class)->getNames(true),
                'switches'          => D2EM::getRepository(Switcher::class)->getNames(true),
                'switchPorts'       => D2EM::getRepository(Switcher::class)->getAllPortForASwitch($patchPanelPort->getSwitchId(),null, $patchPanelPort->getSwitchPortId()),
                'patchPanelPort'    => $patchPanelPort,
                'partnerPorts'      => $partnerPorts,
                'hasDuplex'         => $hasDuplex
        ]);
    }

    /**
     * Allow to edit the information of the patch panel port
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     * @params  $request instance of the current HTTP request
     * @parama  int $id patch panel port that need to be edited
     * @return  redirect
     */
    public function store(StorePatchPanelPort $request){
        if( $request->input( 'id', false ) ) {
            // get the existing patch panel object for that ID
            if( !( $patchPanelPort = D2EM::getRepository( PatchPanelPort::class )->find( $request->input( 'id' ) ) ) ) {
                Log::notice( 'Unknown patch panel port when editing patch panel' );
                abort(404);
            }
        }
        else {
            $patchPanelPort = new PatchPanelPort();
        }

        if($request->input('switch_port')){
            if( !( $switchPort = D2EM::getRepository( SwitchPort::class )->find( $request->input( 'switch_port' ) ) ) ) {
                Log::notice( 'Unknown switch port when adding patch panel' );
                abort(404);
            }

            if($switchPort->getId() != $patchPanelPort->getSwitchPortId()){
                if(D2EM::getRepository(PatchPanelPort::class)->isSwitchPortAvailable($switchPort->getId())){
                    $patchPanelPort->setSwitchPort($switchPort);
                }
                else{
                    return Redirect::to('patch-panel-port/edit/'.$request->input( 'id' ))
                        ->with('fail', 'The switch port selected is already used by an other patch panel Port !')
                        ->withInput(Input::all());
                }
            }

            if($request->input('customer')){
                $custId = D2EM::getRepository(SwitchPort::class)->getCustomerForASwitchPort($switchPort->getId());

                if($custId != null){
                    if($custId != $request->input('customer')){
                        return Redirect::to('patch-panel-port/edit/'.$request->input( 'id' ))
                            ->with('fail', 'Customer not allowed for this switch port !')
                            ->withInput(Input::all());
                    }
                }
            }

        }
        else{
            if($request->input('customer') and $request->input('switch')){
                return Redirect::to('patch-panel-port/edit/'.$request->input( 'id' ))
                    ->with('fail', 'You need to select a switch port !')
                    ->withInput(Input::all());
            }
            $patchPanelPort->setSwitchPort(null);
        }

        if($patchPanelPort->getState() != $request->input('state')){
            $patchPanelPort->setState($request->input('state'));
            $patchPanelPort->setLastStateChange(new \DateTime(date('Y-m-d')));
        }

        $patchPanelPort->setCustomer(($request->input('customer')) ? D2EM::getRepository(Customer::class)->find($request->input('customer')) : null);

        $patchPanelPort->setNotes(($request->input('notes') == '' ? null : $request->input('notes')));

        $patchPanelPort->setAssignedAt(($request->input('assigned_at') == '' ? null : new \DateTime($request->input('assigned_at'))));
        $patchPanelPort->setConnectedAt(($request->input('connected_at') == '' ? null : new \DateTime($request->input('connected_at'))));

        $patchPanelPort->setCeaseRequestedAt(($request->input('ceased_requested_at') == '' ? null : new \DateTime($request->input('ceased_requested_at'))));
        $patchPanelPort->setCeasedAt(($request->input('ceased_at') == '' ? null : new \DateTime($request->input('ceased_at'))));

        $patchPanelPort->setInternalUse($request->input('internal_use'));
        $patchPanelPort->setChargeable($request->input('chargeable'));

        D2EM::persist($patchPanelPort);

        if($request->input('duplex')){
            if($patchPanelPort->hasSlavePort()){
                $isNewSlavePort = false;
                $partnerPort = $patchPanelPort->getDuplexSlavePort();
            }
            else{
                $isNewSlavePort = true;
                $partnerPort = D2EM::getRepository(PatchPanelPort::class)->find($request->input('partner_port'));
            }

            $duplexPort = $patchPanelPort->setDuplexPort($partnerPort,$isNewSlavePort);

            if($isNewSlavePort){
                $patchPanelPort->addDuplexSlavePort($duplexPort);
            }
        }
        D2EM::flush($patchPanelPort);

        return Redirect::to('patch-panel-port/list');

    }

    /**
     * Get the switch port for a Switch
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     * @params  $request instance of the current HTTP request
     * @return  JSON array of listPort
     */
    public function getSwitchPort(Request $request){
        $listPorts = D2EM::getRepository(Switcher::class)->getAllPortForASwitch($request->input('switchId'),$request->input('customerId'),$request->input('switchPortId'));
        return response()->json(array('success' => true, 'response' => $listPorts));
    }

    /**
     * Get the customer for a switch port
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     * @params  $request instance of the current HTTP request
     * @return  JSON customer object
     */
    public function getCustomerForASwitchPort(Request $request){
        $switchPort = D2EM::getRepository(SwitchPort::class)->find($request->input('switchPortId'));
        $success = false;
        $customer = null;
        if($switchPort != null){
            $physicalInterface = $switchPort->getPhysicalInterface();
            if($physicalInterface != null){
                $virtualInterface = $physicalInterface->getVirtualInterface();
                if($virtualInterface != null){
                    $cust = $virtualInterface->getCustomer();
                    if($cust != null){
                        $customer = array('id' => $cust->getId(), 'name' => $cust->getName());
                        $success = true;
                    }
                }
            }
        }
        return response()->json(array('success' => $success, 'response' => $customer));
    }

    /**
     * Get the customer for a switch port
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     * @params  $request instance of the current HTTP request
     * @return  JSON customer object
     */
    public function getSwitchForACustomer(Request $request){
        $customer = D2EM::getRepository(Customer::class)->find($request->input('customerId'));
        $success = false;
        $listSwitches = array();
        if($customer != null){
            $virtualInterfaces = $customer->getVirtualInterfaces();
            foreach($virtualInterfaces as $vi){
                $physicalInterfaces = $vi->getPhysicalInterfaces();
                foreach($physicalInterfaces as $pi){
                    $switchPort = $pi->getSwitchPort();
                    $success = true;
                    $switches = $switchPort->getSwitcher();
                    $listSwitches[$switches->getId()] = $switches->getName();
                }
            }
        }
        return response()->json(array('success' => $success, 'response' => $listSwitches));
    }

    /**
     * Display the patch panel port informations
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     * @params  int $id ID of the patch panel
     * @return  view
     */
    public function view(int $id = null)
    {
        $patchPanelPort = false;

        if( !($patchPanelPort = D2EM::getRepository(PatchPanelPort::class)->find($id))){
            abort(404);
        }
        return view('patch-panel-port/view')->with(['patchPanelPort'    => $patchPanelPort]);
    }
}