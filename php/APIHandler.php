<?php
/**
 * @file        APIHandler.php
 * @brief       API Requests
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

namespace creamy;

// dependencies
require_once('CRMDefaults.php');
require_once('LanguageHandler.php');
require_once('CRMUtils.php');
require_once('goCRMAPISettings.php');
require_once('SessionHandler.php');
$session_class = new \creamy\SessionHandler();

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if(isset($_SESSION["user"])){
	define("session_user", $_SESSION["user"]);
	define("session_usergroup", $_SESSION["usergroup"]);
	define("session_password", $_SESSION["phone_this"]);
	//define("responsetype", "json");
}
/**
 *  APIHandler.
 *  This class is in charge of storing the API Connections for the basic functionality of the system.
 */
 class APIHandler {

	// language handler
	private $lh;

	/** Creation and class lifetime management */

	/**
     * Returns the singleton instance of UIHandler.
     * @staticvar APIHandler $instance The APIHandler instance of this class.
     * @return APIHandler The singleton instance.
     */
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }


    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup()
    {
    }

    /*
     * API_Request - Handles All API Requests
     * @param String $folder - Folder Name where API is located (ex. goUsers, goInbound, goVoicemails)
     * @param Array $postfields - Post Requests. API Name is required (ex. goAction => goGetUserGroupInfo, goAction => goGetAllUsers, goAction => goEditDID)
     * @param Boolean $request_data - true or false. If true, converts return data to original format without json_decode. Returns json_decoded data if false.
     * @return Array $output
    */
    public function API_Request($folder, $postfields, $request_data = false){
		$url = gourl."/".$folder."/goAPI.php";
		$responsetype = "json";

		// Constant Data to be passed
		$default_entries = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'responsetype' => $responsetype,
			'session_user' => session_user,
			'log_user' => session_user,
			'log_group' => session_usergroup,
			'log_ip' => $_SERVER['REMOTE_ADDR'],
			'hostname' => $_SERVER['REMOTE_ADDR']);

		$postdata = array_merge($default_entries, $postfields);

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
		$data = curl_exec($ch);
		curl_close($ch);
	    $output = json_decode($data);
	    
	    if($request_data === true)
	    	return $data;
	    else
			return $output;
	}

	/*
     * API_Upload - Handles All API with Upload. Examples: Upload Leads, Upload Voicefiles
     * @param String $folder - Folder Name where API is located (ex. goUsers, goInbound, goVoicemails)
     * @param Array $postfields - Post Requests. API Name is required (ex. goAction => goGetUserGroupInfo, goAction => goGetAllUsers, goAction => goEditDID)
     * 
     * @return Array $output
    */
	public function API_Upload($folder, $postfields){
		$url = gourl."/".$folder."/goAPI.php";
		$responsetype = "json";
		
		// Constant Data to be passed
		$default_entries = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'responsetype' => $responsetype,
			'session_user' => session_user,
			'log_user' => session_user,
			'log_group' => session_usergroup,
			'log_ip' => $_SERVER['REMOTE_ADDR'],
			'hostname' => $_SERVER['REMOTE_ADDR']);

		$postdata = array_merge($default_entries, $postfields);

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT , 0); //gg
		curl_setopt ($ch, CURLOPT_TIMEOUT  , 10000); //gg
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);
	    
		return $output;
	}

    public function API_getGOPackage(){
		$postfields = array(
			'goAction' => 'goGetPackage'
		);				

		return $this->API_Request("goPackages", $postfields);
	}

    public function API_goGetGroupPermission() {
		$postfields = array(
			'goAction' => 'goGetUserGroupInfo',
			'user_group' => session_usergroup
		);

		return $this->API_Request("goUserGroups", $postfields);
	}

    public function goGetPermissions($type = 'dashboard') {
		
		$permissions = $this->API_goGetGroupPermission();
		$decoded_permission = json_decode($permissions->data->permissions);
		
		$return = NULL;
		if (!empty($permissions)) {
			$types = explode(",", $type);
			if (count($types) > 1) {
				foreach ($types as $t) {
					if (array_key_exists($t, $decoded_permission)) {
						$return->{$t} = $decoded_permission->{$t};
					}
				}
			} else {
				if ($type == 'sidebar') {
					$return = $permissions;
				} else if (array_key_exists($type, $decoded_permission)) {
					$return = $decoded_permission->{$type};
				} else {
					$return = null;
				}
			}
		}

		return $return;
	}
	
	public function API_getAllPauseCodes($campaign_id) {
		$postfields = array(
			'goAction' => 'goGetAllPauseCodes',
			'campaign_id' => $campaign_id
		);	

		return $this->API_Request("goPauseCodes", $postfields);
	}
	
	public function API_modifyPauseCode($postfields) {
		return $this->API_Request("goPauseCodes", $postfields);
	}	
	
	public function API_getAllInGroups() {
		$postfields = array(
			'goAction' => 'goGetAllIngroup'
		);	

		return $this->API_Request("goInbound", $postfields);
	}

	public function API_modifyInGroups($postfields) {
		return $this->API_Request("goInbound", $postfields);
	}

	public function API_getInGroupInfo($groupid) {
		$postfields = array(
			'goAction' => 'goGetIngroupInfo',
			'group_id' => $groupid
		);				
		return $this->API_Request("goInbound", $postfields);
	}

	// Telephony IVR
	public function API_getAllIVRs() {
		$postfields = array(
			'goAction' => 'goGetAllIVR'
		);
		return $this->API_Request("goInbound", $postfields);
	}
	
	public function API_getIVRInfo($menu_id) {
		$postfields = array(
			'goAction' => 'goGetIVRInfo',
			'menu_id' => $menu_id
		);
		return $this->API_Request("goInbound", $postfields);
	}	

	public function API_getIVROptions($menu_id) {
		$postfields = array(
			'goAction' => 'goGetIVROptions',
			'menu_id' => $menu_id
		);
		return $this->API_Request("goInbound", $postfields);
	}
	
	public function API_modifyIVR($postfields) {
		return $this->API_Request("goInbound", $postfields);
	}

	//Telephony > phonenumber(DID)
	public function API_getAllDIDs() {
		$postfields = array(
			'goAction' => 'goGetAllDID'
		);				
		return $this->API_Request("goInbound", $postfields);
	}

	// Telephony Users -> Phone
	public function API_getAllPhones(){
		$postfields = array(
			'goAction' => 'goGetAllPhones'
		);				
		return $this->API_Request("goPhones", $postfields);
	}

	public function API_getPhoneInfo($extenid){
		$postfields = array(
			'goAction' => 'goGetPhoneInfo',
			'extension' => $extenid
		);				
		return $this->API_Request("goPhones", $postfields);
	}
	
	/** Call Times API - Get all list of call times */
	public function API_getAllCalltimes(){
        $postfields = array(
			'goAction' => 'goGetAllCalltimes'
		);				
        return $this->API_Request("goCalltimes", $postfields);
	}

	public function API_getCalltimeInfo($call_time_id){
        $postfields = array(
			'goAction' => 'goGetCalltimeInfo',
			'call_time_id' => $call_time_id
		);				
        return $this->API_Request("goCalltimes", $postfields);
	}
	
	// API Scripts
	public function API_getAllScripts(){
		$url = gourl."/goScripts/goAPI.php";
        $postfields = array(
			'goAction' => 'goGetAllScripts'
		);				
        return $this->API_Request("goScripts", $postfields);
	}

	public function API_getScriptInfo($scriptid){
		$url = gourl."/goScripts/goAPI.php";
        $postfields = array(
			'goAction' => 'goGetScriptInfo',
			'script_id' => $scriptid
		);				
        return $this->API_Request("goScripts", $postfields);
	}
	
	// VoiceMails
	public function API_getAllVoiceMails() {
		$postfields = array(
			'goAction' => 'goGetAllVoicemails'
		);				
		return $this->API_Request("goVoicemails", $postfields);
	}

	public function API_getVoicemailInfo($voicemail_id) {
		$postfields = array(
			'goAction' => 'goGetVoicemailInfo',
			'voicemail_id' => $voicemail_id
		);				
		return $this->API_Request("goVoicemails", $postfields);
	}
	
	/** Voice Files API - Get all list of voice files */
	public function API_getAllVoiceFiles(){
		$postfields = array(
			'goAction' => 'goGetAllVoiceFiles'
		);				
		return $this->API_Request("goVoiceFiles", $postfields);
	}

	/** Music On Hold API - Get all list of music on hold */
	public function API_getAllMusicOnHold(){
		$postfields = array(
			'goAction' => 'goGetAllMusicOnHold'
		);
		return $this->API_Request("goMusicOnHold", $postfields);
	}
	
	public function API_getAllCampaigns(){
		$postfields = array(
			'goAction' => 'goGetAllCampaigns'
		);		
		return $this->API_Request("goCampaigns", $postfields);
	}	
	
	public function API_getSuggestedDIDs($keyword){
		$postfields = array(
			'goAction' => 'goGetSuggestedDIDs',
			'keyword' => $keyword			
		);
		return $this->API_Request("goCampaigns", $postfields);
	}	
	
	public function API_getDIDSettings($did){
		$postfields = array(
			'goAction' => 'goGetDIDSettings',
			'did' => $did			
		);
		return $this->API_Request("goCampaigns", $postfields);
	}
	
	public function getAllCampaignStatuses(){
        $campaign = $this->API_getAllCampaigns();
        for($i=0;$i < count($campaign->campaign_id);$i++){
	        $campdialStatus = $this->API_getAllCampaignDialStatuses($campaign->campaign_id[$i]);
			for($x=0;$x<count($campdialStatus->status);$x++){
				$status[] = $campdialStatus->status[$x];
				$status_name[] = $campdialStatus->status_name[$x];
			}
			$output = array("status" => $status, "status_name" => $status_name);
		}
		return $output;
	}
		
	/*
	 * Displaying Disposition
	 * [[API: Function]] - getAllDispositions
	 * 	This application is used to get list of campaign belongs to user.
	*/
	public function API_getAllDispositions($custom){
		$postfields = array(
			'goAction' => 'goGetAllDispositions',
			'custom_request' => $custom
		);		
		return $this->API_Request("goDispositions", $postfields);
	}	
	
	public function API_getDispositionInfo($campid){
		$postfields = array(
			'goAction' => 'goGetDispositionInfo',
			'campaign_id' => $campid,
			'user_group' => session_usergroup
		);		
		return $this->API_Request("goDispositions", $postfields);
	}
	
	public function API_getAllLeadRecycling(){
		$postfields = array(
			'goAction' => 'goGetAllLeadRecycling'
		);		
		return $this->API_Request("goLeadRecycling", $postfields);
	}	
	
	public function API_getLeadRecyclingInfo($campaign_id){
		$postfields = array(
			'goAction' => 'goGetLeadRecyclingInfo',
			'campaign_id' => $campaign_id
		);		
		return $this->API_Request("goLeadRecycling", $postfields);
	}
	
	public function API_getAllDialStatuses($campaign_id){
		$postfields = array(
			'goAction' => 'goGetAllDialStatuses',
			'campaign_id' => $campaign_id,
			'hotkeys_only' => "1"
		);		
		return $this->API_Request("goDialStatus", $postfields);
	}	
	
	public function API_getAllDialStatusesSurvey($campaign_id){
		$postfields = array(
			'goAction' => 'goGetAllDialStatuses',
			'campaign_id' => $campaign_id,
			'hotkeys_only' => "1"
		);		
		return $this->API_Request("goDialStatus", $postfields);
	}
	
	public function API_getAllHotkeys($campaign_id) {
		$postfields = array(
			'goAction' => 'goGetAllHotkeys',
			'campaign_id' => $campaign_id
		);	

		return $this->API_Request("goHotkeys", $postfields);
	}	
	/*
	 * Displaying Lead Filter
	 * [[API: Function]] - getAllLeadFilters
	 * 	This application is used to get list of lead filter belongs to user.
	*/
	public function API_getAllLeadFilters(){
		$postfields = array(
			'goAction' => 'goGetAllLeadFilters'
		);		
		return $this->API_Request("goLeadFilters", $postfields);
	}	
	
	public function API_getCountryCodes(){
		$postfields = array(
			'goAction' => 'getAllCountryCodes'
		);		
		return $this->API_Request("goCountryCode", $postfields);
	}	
	
	public function API_getAllLists(){
		$postfields = array(
			'goAction' => 'goGetAllLists',
			'user_group' => session_usergroup
		);		
		return $this->API_Request("goLists", $postfields);
	}	
	
	public function API_getAllListsCampaign($campaign_id){
		$postfields = array(
			'goAction' => 'goGetAllListsCampaign',
			'campaign_id' => $campaign_id
		);		
		return $this->API_Request("goLists", $postfields);
	}
	
	public function API_getStatusesWithCountCalledNCalled($list_id){
		$postfields = array(
			'goAction' => 'goGetStatusesWithCountCalledNCalled',
			'list_id' => $list_id
		);		
		return $this->API_Request("goLists", $postfields);
	}
	
	public function API_getAllLeadsOnHopper($campaign_id){
		$postfields = array(
			'goAction' => 'goGetAllLeadsOnHopper',
			'campaign_id' => $campaign_id
		);		
		return $this->API_Request("goLists", $postfields);
	}
	
	public function API_getAllCarriers(){
		$postfields = array(
			'goAction' => 'goGetAllCarriers'
		);		
		return $this->API_Request("goCarriers", $postfields);
	}	
	
	public function API_getCarrierInfo($carrier_id){
		$postfields = array(
			'goAction' => 'goGetCarrierInfo',
			'carrier_id' => $carrier_id
		);		
		return $this->API_Request("goCarriers", $postfields);
	}	
	
	public function API_getAllServers(){
		$postfields = array(
			'goAction' => 'goGetAllServers'
		);		
		return $this->API_Request("goServers", $postfields);
	}	
	
	public function API_getServerInfo($server_id){
		$postfields = array(
			'goAction' => 'goGetServerInfo',
			'server_id' => $server_id
		);		
		return $this->API_Request("goServers", $postfields);
	}
	
	public function API_getAdminLogsList(){
		$postfields = array(
			'goAction' => 'goGetAdminLogsList'
		);		
		return $this->API_Request("goAdminLogs", $postfields);
	}	
	
	public function API_getAllCampaignDialStatuses($campaign_id){
		$postfields = array(
			'goAction' => 'goGetAllCampaignDialStatuses',
			'campaign_id' => $campaign_id
		);		
		return $this->API_Request("goDialStatus", $postfields);
	}	
	
	public function API_getCampaignInfo($campid){
		$postfields = array(
			'goAction' => 'goGetCampaignInfo',
			'campaign_id' => $campid,
			'user_group' => session_usergroup			
		);		
		return $this->API_Request("goCampaigns", $postfields);
	}	
	
	public function API_getAllUsers(){
		$postfields = array(
			'goAction' => 'goGetAllUsers'
		);
		return $this->API_Request("goUsers", $postfields);
	}

	public function API_getUserInfo($user_id){
		$postfields = array(
			'goAction' => 'goGetUserInfoNew',
			'user_id' => $user_id
		);
		return $this->API_Request("goUsers", $postfields);
	}
	
	public function API_getAgentLog($user, $sdate, $edate, $agentlog){
		$postfields = array(
			'goAction' => 'goGetAgentLog',
			'user' => $user,
			'start_date' => $sdate,
			'end_date' => $edate,
			'agentlog'	=> $agentlog
		);
		return $this->API_Request("goUsers", $postfields);
	}
	
	public function API_getAllUserGroups() {
		$postfields = array(
			'goAction' => 'goGetAllUserGroups'
		);
		return $this->API_Request("goUserGroups", $postfields);
	}
	
	public function API_getUserGroupInfo($group_id) {
		$postfields = array(
			'goAction' => 'goGetUserGroupInfo',
			'user_group' => $group_id
		);
		return $this->API_Request("goUserGroups", $postfields);
	}
	
	public function API_getCallRecordingList($search_phone, $start_filterdate, $end_filterdate, $agent_filter) {
		$postfields = array(
			'goAction' => 'goGetCallRecordingList'
		);
		if (isset($search_phone)) { 
			$postfields .= array(
				'requestDataPhone' => $search_phone
			);
		}
	    if (isset($start_filterdate)) {
			$postfields .= array(
				'start_filterdate' => $start_filterdate,
				'end_filterdate' => $end_filterdate,
				'agent_filter' => $agent_filter
			);	    
	    }
		return $this->API_Request("goCallRecordings", $postfields);
	}	
	
	public function API_actionDNC($postfields) {
		return $this->API_Request("goLists", $postfields);
	}

	public function API_SMTPActivation($postfields){
		return $this->API_Request("goSMTP", $postfields);
	}

	public function API_addCalltime($postfields){
		return $this->API_Request("goCalltimes", $postfields);
	}

	public function API_addCarrier($postfields){
		return $this->API_Request("goCarriers", $postfields);
	}

	public function API_editCarrier($postfields){
		return $this->API_Request("goCarriers", $postfields);
	}
	
	public function API_addCustomFields($postfields){
		return $this->API_Request("goCustomFields", $postfields);
	}

	public function API_addDialStatus($postfields){
		return $this->API_Request("goCampaigns", $postfields);
	}

	public function API_addDisposition($postfields){
		return $this->API_Request("goDispositions", $postfields);
	}
	
	public function API_addGoogleSheet($postfields){
		return $this->API_Request("goCampaigns", $postfields);
	}

	public function API_addHotkey($postfields){
		return $this->API_Request("goHotkeys", $postfields);
	}

	public function API_addIVR($postfields){
		return $this->API_Request("goInbound", $postfields);
	}

	public function API_addLeadFilter($postfields){
		return $this->API_Request("goLeadFilters", $postfields);
	}

	public function API_addLeadRecycling($postfields){
		return $this->API_Request("goLeadRecycling", $postfields);
	}

	public function API_addLoadLeads($postfields){
		return $this->API_Upload("goUploadLeads", $postfields);
	}

	public function API_addMOH($postfields){
		return $this->API_Request("goMusicOnHold", $postfields);
	}

	public function API_addPauseCode($postfields){
		return $this->API_Request("goPauseCodes", $postfields);
	}

	public function API_addScript($postfields){
		return $this->API_Request("goScripts", $postfields);
	}

	public function API_addServer($postfields){
		return $this->API_Request("goServers", $postfields);
	}

	public function API_editServer($postfields){
		return $this->API_Request("goServers", $postfields);
	}
	
	public function API_addUser($postfields){
		return $this->API_Request("goUsers", $postfields);
	}

	public function API_addPhones($postfields){
		return $this->API_Request("goPhones", $postfields);
	}

	public function API_editPhone($postfields){
		return $this->API_Request("goPhones", $postfields);
	}
	
	public function API_addIngroup($postfields){
		return $this->API_Request("goInbound", $postfields);
	}

	public function API_addDID($postfields){
		return $this->API_Request("goInbound", $postfields);
	}

	public function API_addList($postfields){
		return $this->API_Request("goLists", $postfields);
	}

	public function API_addUserGroup($postfields){
		return $this->API_Request("goUserGroups", $postfields);
	}

	public function API_editUserGroup($postfields){
		return $this->API_Request("goUserGroups", $postfields);
	}
	
	public function API_addVoiceFiles($postfields){
		return $this->API_Upload("goVoiceFiles", $postfields);
	}

	public function API_addVoicemail($postfields){
		return $this->API_Request("goVoicemails", $postfields);
	}

	public function API_editVoicemail($postfields){
		return $this->API_Request("goVoicemails", $postfields);
	}
	
	public function API_checkCalltimes($postfields){
		return $this->API_Request("goCalltimes", $postfields);
	}

	public function API_checkCampaign($postfields){
		return $this->API_Request("goCampaigns", $postfields);
	}

	public function API_checkUser($postfields){
		return $this->API_Request("goUsers", $postfields);
	}
}
?>
