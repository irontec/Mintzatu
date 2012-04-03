<?php
// Jabi: La clase CMISService - que nombra el fichero, está definida más abajo
// Fichero cogido del plugin de WP.

//  Author: Rich McKnight rich.mcknight@alfresco.com http://oldschooltechie.com

// Option Contants for Array Indexing
// -- Generally optional flags that control how much information is returned
// -- Change log token is an anomoly -- but included in URL as parameter
define("OPT_MAX_ITEMS","maxItems");
define("OPT_SKIP_COUNT","skipCount");
define("OPT_FILTER","filter");
define("OPT_INCLUDE_PROPERTY_DEFINITIONS","includePropertyDefinitions");
define("OPT_INCLUDE_RELATIONSHIPS","includeRelationships");
define("OPT_INCLUDE_POLICY_IDS","includePolicyIds");
define("OPT_RENDITION_FILTER","renditionFilter");
define("OPT_INCLUDE_ACL","includeACL");
define("OPT_INCLUDE_ALLOWABLE_ACTIONS","includeAllowableActions");
define("OPT_DEPTH","depth");
define("OPT_CHANGE_LOG_TOKEN","changeLogToken");

define("LINK_ALLOWABLE_ACTIONS","http://docs.oasis-open.org/ns/cmis/link/200908/allowableactions");

define("MIME_ATOM_XML",'application/atom+xml');
define("MIME_ATOM_XML_ENTRY",'application/atom+xml;type=entry');
define("MIME_ATOM_XML_FEED",'application/atom+xml;type=feed');
define("MIME_CMIS_TREE",'application/cmistree+xml');
define("MIME_CMIS_QUERY",'application/cmisquery+xml');


// Many Links have a pattern to them based upon objectId -- but can that be depended upon?

class cmis_service extends cmis_repositorywrapper {
	var $_link_cache;
    
	function __construct($url,$username,$password,$options=null) {
		parent::__construct($url,$username,$password,$options);
		$this->_link_cache=array();
		$this->_title_cache=array();
		$this->_objTypeId_cache=array();
		$this->_type_cache=array();
	}

	// Utility Methods -- Added Titles
	// Should refactor to allow for single object
	function cacheEntryInfo($obj) {
			$this->_link_cache[$obj->id]=$obj->links;
			$this->_title_cache[$obj->id]=$obj->properties["cmis:name"]; // Broad Assumption Here?
			$this->_objTypeId_cache[$obj->id]=$obj->properties["cmis:objectTypeId"];
	}

	function cacheFeedInfo ($objs) {
		foreach ($objs->objectList as $obj) {
			$this->cacheEntryInfo($obj);
		}
	}

	function cacheTypeInfo ($tDef) {
		  $this->_type_cache[$tDef->id] = $tDef;
	}

	function getPropertyType($typeId,$propertyId) {
		if ($this->_type_cache[$typeId]) {
			return $this->_type_cache[$typeId]->properties[$propertyId]["cmis:propertyType"];
		}
		$obj=$this->getTypeDefinition($typeId);
		return $obj->properties[$propertyId]["cmis:propertyType"];
	}

	function getObjectType($objectId) {
		if ($this->_objTypeId_cache[$objectId]) {
			return $this->_objTypeId_cache[$objectId];
		}
		$obj=$this->getObject($objectId);
		return $obj->properties["cmis:objectTypeId"];
	}

	function getTitle($objectId) {
		if ($this->_title_cache[$objectId]) {
			return $this->_title_cache[$objectId];
		}
		$obj=$this->getObject($objectId);
		return $obj->properties["cmis:name"];
	}
	function getLink($objectId,$linkName) {
		if ($this->_link_cache[$objectId][$linkName]) {
			return $this->_link_cache[$objectId][$linkName];
		}
		$obj=$this->getObject($objectId);
		return $obj->links[$linkName];
	}

	// Repository Services
	function getRepositories() {
		throw new Exception("Not Implemented");
	}

	function getRepositoryInfo() {
		return $this->workspace;
	}

	function getTypeChildren() {
		throw new Exception("Not Implemented");
	}

	function getTypeDescendants() {
		throw new Exception("Not Implemented");
	}

	function getTypeDefinition($typeId,$options=array()) { // Nice to have
		$varmap=$options;
		$varmap["id"]=$typeId;
		$myURL = $this->processTemplate($this->workspace->uritemplates['typebyid'],$varmap);
		$ret=$this->doGet($myURL);
		$obj=$this->extractTypeDef($ret->body);
		$this->cacheTypeInfo($obj);
		return $obj;
	}

	function getObjectTypeDefinition($objectId) { // Nice to have
		$myURL = $this->getLink($objectId,"describedby");

		$ret=$this->doGet($myURL);
		$obj=$this->extractTypeDef($ret->body);
		$this->cacheTypeInfo($obj);
		return $obj;
	}
	//Navigation Services
	function getFolderTree() { // Would Be Useful
		throw new Exception("Not Implemented");
	}

	function getDescendants() { // Nice to have
		throw new Exception("Not Implemented");
	}

	function getChildren($objectId,$options=array()) {
		$myURL = $this->getLink($objectId,"down");
		//TODO: Need GenURLQueryString Utility
		
		$ret=$this->doGet($myURL);
		$objs=$this->extractObjectFeed($ret->body);
		$this->cacheFeedInfo($objs);
		return $objs;
	}

	function getFolderParent($objectId,$options=array()) { //yes
		$myURL = $this->getLink($objectId,"up");

		//TODO: Need GenURLQueryString Utility
		$ret=$this->doGet($myURL);
		$obj=$this->extractObjectEntry($ret->body);
		$this->cacheEntryInfo($obj);
		return $obj;
	}

	function getObjectParents($objectId,$options=array()) { // yes
		$myURL = $this->getLink($objectId,"up");

		//TODO: Need GenURLQueryString Utility
		$ret=$this->doGet($myURL);
		$objs=$this->extractObjectFeed($ret->body);
		$this->cacheFeedInfo($objs);
		return $objs;
	}

	function getCheckedOutDocs($options=array()) {
 		$obj_url = $this->workspace->collections['checkedout'];
		$ret = $this->doGet($obj_url);
		$objs=$this->extractObjectFeed($ret->body);
		$this->cacheFeedInfo($objs);
		return $objs;
	}

	//Discovery Services

	static function getQueryTemplate() {
		ob_start();
		echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
?>
<cmis:query xmlns:cmis="http://docs.oasis-open.org/ns/cmis/core/200908/"
xmlns:cmism="http://docs.oasis-open.org/ns/cmis/messaging/200908/"
xmlns:atom="http://www.w3.org/2005/Atom"
xmlns:app="http://www.w3.org/2007/app"
xmlns:cmisra="http://docs.oasisopen.org/ns/cmis/restatom/200908/">
<cmis:statement>{q}</cmis:statement>
<cmis:searchAllVersions>{searchAllVersions}</cmis:searchAllVersions>
<cmis:includeAllowableActions>{includeAllowableActions}</cmis:includeAllowableActions>
<cmis:includeRelationships>{includeRelationships}</cmis:includeRelationships>
<cmis:renditionFilter>{renditionFilter}</cmis:renditionFilter>
<cmis:maxItems>{maxItems}</cmis:maxItems>
<cmis:skipCount>{skipCount}</cmis:skipCount>
</cmis:query>
<?php
		return ob_get_clean();
	}
	function query($q,$options=array()) {
		static $query_template;
		if (!isset($query_template)) {
			$query_template = CMISService::getQueryTemplate();
		}
		$hash_values=$options;
		$hash_values['q'] = $q;
		$post_value = CMISRepositoryWrapper::processTemplate($query_template,$hash_values);
		$ret = $this->doPost($this->workspace->collections['query'],$post_value,MIME_CMIS_QUERY);
		$objs = $this->extractObjectFeed($ret->body);
		$this->cacheFeedInfo($objs);
 		return $objs;
	}

	function getContentChanges() {
		throw new Exception("Not Implemented");
	}

	//Object Services
	static function getEntryTemplate() {
		ob_start();
		echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
?>
<atom:entry xmlns:cmis="http://docs.oasis-open.org/ns/cmis/core/200908/"
xmlns:cmism="http://docs.oasis-open.org/ns/cmis/messaging/200908/"
xmlns:atom="http://www.w3.org/2005/Atom"
xmlns:app="http://www.w3.org/2007/app"
xmlns:cmisra="http://docs.oasis-open.org/ns/cmis/restatom/200908/">
<atom:title>{title}</atom:title>
{SUMMARY}
{CONTENT}
<cmisra:object><cmis:properties>{PROPERTIES}</cmis:properties></cmisra:object>
</atom:entry>
<?php
		return ob_get_clean();
	}

	static function getPropertyTemplate() {
		ob_start();
?>
		<cmis:property{propertyType} propertyDefinitionId="{propertyId}">
			<cmis:value>{properties}</cmis:value>
		</cmis:property{propertyType}>
<?php
		return ob_get_clean();
	}

	function processPropertyTemplates($objectType,$propMap) {
		static $propTemplate;
		static $propertyTypeMap;
		if (!isset($propTemplate)) {
			$propTemplate = CMISService::getPropertyTemplate();
		}
		if (!isset($propertyTypeMap)) { // Not sure if I need to do this like this
			$propertyTypeMap = array (
				"integer" => "Integer",
				"boolean" => "Boolean",
				"datetime" => "DateTime",
				"decimal" => "Decimal",
				"html" => "Html",
				"id" => "Id",
				"string" => "String",
				"url" => "Url",
				"xml" => "Xml",
			);
		}
		$propertyContent="";
		$hash_values=array();
		foreach ($propMap as $propId => $propValue) {
			$hash_values['propertyType']=$propertyTypeMap[$this->getPropertyType($objectType,$propId)];
			$hash_values['propertyId']=$propId;
			if (is_array($propValue)) {
				$first_one=true;
				$hash_values['properties']="";
				foreach ($propValue as $val) {
					//This is a bit of a hack
					if ($first_one) {
						$first_one=false;
					} else {
						$hash_values['properties'] .= "</cmis:values>\n<cmis:values>";
					}
					$hash_values['properties'] .= $val;
				}
			} else {
				$hash_values['properties']=$propValue;
			}
			//echo "HASH:\n";
			//print_r(array("template" =>$propTemplate, "Hash" => $hash_values));
			$propertyContent  .= CMISRepositoryWrapper::processTemplate($propTemplate,$hash_values);
		}
		return $propertyContent;
	}

	static function getContentEntry($content,$content_type="application/octet-stream") {
		static $contentTemplate;
		if (!isset($contentTemplate)) {
			$contentTemplate = CMISService::getContentTemplate();
		}
		if ($content) {
			return CMISRepositoryWrapper::processTemplate($contentTemplate,array("content" => base64_encode($content),"content_type" => $content_type));
		} else {
			return "";
		}
	}

	static function getSummaryTemplate() {
		ob_start();
?>
		<atom:summary>{summary}</atom:summary>
<?php
		return ob_get_clean();
	}

	static function getContentTemplate() {
		ob_start();
?>
		<cmisra:content>
			<cmisra:mediatype>
				{content_type}
			</cmisra:mediatype>
			<cmisra:base64>
				{content}
			</cmisra:base64>
		</cmisra:content>
<?php
		return ob_get_clean();
	}
	static function createAtomEntry($name,$properties) {

	}
	function getObject($objectId,$options=array()) {
		$varmap=$options;
		$varmap["id"]=$objectId;
 		$obj_url = $this->processTemplate($this->workspace->uritemplates['objectbyid'],$varmap);
		$ret = $this->doGet($obj_url);
		$obj=$this->extractObject($ret->body);
		$this->cacheEntryInfo($obj);
 		return $obj;
	}

	function getObjectByPath($path,$options=array()) {
		$varmap=$options;
		$varmap["path"]=$path;
 		$obj_url = $this->processTemplate($this->workspace->uritemplates['objectbypath'],$varmap);

		$ret = $this->doGet($obj_url);
		$obj=$this->extractObject($ret->body);
		$this->cacheEntryInfo($obj);
 		return $obj;
	}

	function getProperties($objectId,$options=array()) {
		// May need to set the options array default --
		return $this->getObject($objectId,$options);
	}

	function getAllowableActions($objectId,$options=array()) {
		// get stripped down version of object (for the links) and then get the allowable actions?
		// Low priority -- can get all information when getting object
		throw new Exception("Not Implemented");
	}

	function getRenditions($objectId,$options=array(OPT_RENDITION_FILTER => "*")) {
		return getObject($objectId,$options);
	}

	function getContentStream($objectId,$options=array()) { // Yes
		$myURL = $this->getLink($objectId,"edit-media");
		$ret=$this->doGet($myURL);
		// doRequest stores the last request information in this object
		return $ret->body;
	}

	function postObject($folderId,$objectName,$objectType,$properties=array(),$content=null,$content_type="application/octet-stream",$options=array()) { // Yes
		$myURL = $this->getLink($folderId,"down");
		// TODO: Need Proper Query String Handling
		// Assumes that the 'down' link does not have a querystring in it
		$myURL = CMISRepositoryWrapper::getOpUrl($myURL,$options);
		static $entry_template;
		if (!isset($entry_template)) {
			$entry_template = CMISService::getEntryTemplate();
		}
		if (is_array($properties)) {
			$hash_values=$properties;
		} else {
			$hash_values=array();
		}
		if (!isset($hash_values["cmis:objectTypeId"])) {
			$hash_values["cmis:objectTypeId"]=$objectType;
		}
		$properties_xml = $this->processPropertyTemplates($objectType,$hash_values);
		if (is_array($options)) {
			$hash_values=$options;
		} else {
			$hash_values=array();
		}
		$hash_values["PROPERTIES"]=$properties_xml;
		$hash_values["SUMMARY"]=CMISService::getSummaryTemplate();
		if ($content) {
			$hash_values["CONTENT"]=CMISService::getContentEntry($content,$content_type);
		}
		if (!isset($hash_values['title'])) {
			$hash_values['title'] = $objectName;
		}
		if (!isset($hash_values['summary'])) {
			$hash_values['summary'] = $objectName;
		}
		$post_value = CMISRepositoryWrapper::processTemplate($entry_template,$hash_values);
		$ret = $this->doPost($myURL,$post_value,MIME_ATOM_XML_ENTRY);
		// print "DO_POST\n";
		// print_r($ret);
		$obj=$this->extractObject($ret->body);
		$this->cacheEntryInfo($obj);
  		return $obj;
	}

	function createDocument($folderId,$fileName,$properties=array(),$content=null,$content_type="application/octet-stream",$options=array()) { // Yes
		return $this->postObject($folderId,$fileName,"cmis:document",$properties,$content,$content_type,$options);
	}

	function createDocumentFromSource() { //Yes?
		throw new Exception("Not Implemented in This Binding");
	}

	function createFolder($folderId,$folderName,$properties=array(),$options=array()) { // Yes
		return $this->postObject($folderId,$folderName,"cmis:folder",$properties,null,null,$options);
	}

	function createRelationship() { // Not in first Release
		throw new Exception("Not Implemented");
	}

	function createPolicy() { // Not in first Release
		throw new Exception("Not Implemented");
	}

	function updateProperties($objectId,$properties=array(),$options=array()) { // Yes
		$varmap=$options;
		$varmap["id"]=$objectId;
		$objectName=$this->getTitle($objectId);
		$objectType=$this->getObjectType($objectId);
 		$obj_url = $this->getLink($objectId,"edit");
		$obj_url = CMISRepositoryWrapper::getOpUrl($obj_url,$options);
		static $entry_template;
		if (!isset($entry_template)) {
			$entry_template = CMISService::getEntryTemplate();
		}
		if (is_array($properties)) {
			$hash_values=$properties;
		} else {
			$hash_values=array();
		}
		$properties_xml = $this->processPropertyTemplates($objectType,$hash_values);
		if (is_array($options)) {
			$hash_values=$options;
		} else {
			$hash_values=array();
		}
		$hash_values["PROPERTIES"]=$properties_xml;
		$hash_values["SUMMARY"]=CMISService::getSummaryTemplate();
		if (!isset($hash_values['title'])) {
			$hash_values['title'] = $objectName;
		}
		if (!isset($hash_values['summary'])) {
			$hash_values['summary'] = $objectName;
		}
		$put_value = CMISRepositoryWrapper::processTemplate($entry_template,$hash_values);
		$ret= $this->doPut($obj_url,$put_value,MIME_ATOM_XML_ENTRY);
		$obj=$this->extractObject($ret->body);
		$this->cacheEntryInfo($obj);
  		return $obj;
	}

	function moveObject($objectId,$targetFolderId,$sourceFolderId,$options=array()) { //yes
	    $options['sourceFolderId']=$sourceFolderId;
		return $this->postObject($targetFolderId,$this->getTitle($objectId),$this->getObjectType($objectId),array("cmis:objectId" => $objectId),null,null,$options);
	}

	function deleteObject($objectId,$options=array()) { //Yes
		$varmap=$options;
		$varmap["id"]=$objectId;
 		$obj_url = $this->getLink($objectId,"edit");
		$ret = $this->doDelete($obj_url);
		return;
	}

	function deleteTree() { // Nice to have
		throw new Exception("Not Implemented");
	}

	function setContentStream($objectId,$content,$content_type,$options=array()) { //Yes
		$myURL = $this->getLink($objectId,"edit-media");
		$ret=$this->doPut($myURL,$content,$content_type);
	}

	function deleteContentStream($objectId,$options=array()) { //yes
		$myURL = $this->getLink($objectId,"edit-media");
		$ret=$this->doDelete($myURL);
		return;
	}

	//Versioning Services
	function getPropertiesOfLatestVersion($objectId,$options=array()) {
		throw new Exception("Not Implemented");
	}

	function getObjectOfLatestVersion($objectId,$options=array()) {
		throw new Exception("Not Implemented");
	}

	function getAllVersions() {
		throw new Exception("Not Implemented");
	}

	function checkOut() {
		throw new Exception("Not Implemented");
	}

	function checkIn() {
		throw new Exception("Not Implemented");
	}

	function cancelCheckOut() {
		throw new Exception("Not Implemented");
	}

	function deleteAllVersions() {
		throw new Exception("Not Implemented");
	}

	//Relationship Services
	function getObjectRelationships() {
		// get stripped down version of object (for the links) and then get the relationships?
		// Low priority -- can get all information when getting object
		throw new Exception("Not Implemented");
	}

	//Multi-Filing Services
	function addObjectToFolder() { // Probably
		throw new Exception("Not Implemented");
	}

	function removeObjectFromFolder() { //Probably
		throw new Exception("Not Implemented");
	}

	//Policy Services
	function getAppliedPolicies() {
		throw new Exception("Not Implemented");
	}

	function applyPolicy() {
		throw new Exception("Not Implemented");
	}

	function removePolicy() {
		throw new Exception("Not Implemented");
	}

	//ACL Services
	function getACL() {
		throw new Exception("Not Implemented");
	}

	function applyACL() {
		throw new Exception("Not Implemented");
	}
}
