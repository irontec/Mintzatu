<?php

class cmis_repositorywrapper {
    // Handles --
    //   Workspace -- but only endpoints with a single repo
    //   Entry -- but only for objects
    //   Feeds -- but only for non-hierarchical feeds
    // Does not handle --
    //   -- Hierarchical Feeds
    //   -- Types
    //   -- Others?
    // Only Handles Basic Auth
    // Very Little Error Checking
    // Does not work against pre CMIS 1.0 Repos
    var $url;
    var $username;
    var $password;
    var $authenticated;
    var $workspace;
    var $last_request;
    
    protected $_locationPort = false;
    protected $_locationHost = false;

    static $namespaces = array(
        "cmis" => "http://docs.oasis-open.org/ns/cmis/core/200908/",
        "cmisra" => "http://docs.oasis-open.org/ns/cmis/restatom/200908/",
        "atom" => "http://www.w3.org/2005/Atom",
        "app" => "http://www.w3.org/2007/app",
    );

    function __construct($url,$username=null,$password=null,$options=null) {
        $this->connect($url,$username,$password,$options);
    }
    
    public function setLocationPort($port) {
        $this->_locationPort = $port;
    }

    public function setLocationHost($host) {
        $this->_locationHost = $host;
    }

    static function getOpUrl($url,$options=null) {
        if (is_array($options) && (count($options) > 0)) {
            $needs_question=strstr($url,"?") === false;
            return $url . ($needs_question?"?":"&") . http_build_query($options);
        } else {
            return $url;
        }
    }

    function connect($url,$username,$password,$options) {
        // TODO: Make this work with cookies
        $this->url = $url;
        $this->username = $username;
        $this->password = $password;
        $this->auth_options=$options;
        $this->authenticated = false;
        $retval=$this->doGet($this->url);
        if ($retval->code == 200 || $retval->code == 201) {
            $this->authenticated=true;
            $this->workspace = cmis_repositorywrapper::extractWorkspace($retval->body);
        }
    }

    function doGet($url) {
        return $this->doRequest($url);
    }

    function doDelete($url) {
        return $this->doRequest($url,"DELETE");
    }

    function doPost($url,$content,$contentType,$charset=null) {
        return $this->doRequest($url,"POST",$content,$contentType);
    }

    function doPut($url,$content,$contentType,$charset=null) {
        return $this->doRequest($url,"PUT",$content,$contentType);
    }

    function doRequest($url,$method="GET",$content=null,$contentType=null,$charset=null) {
        // Process the HTTP request
        // 'til now only the GET request has been tested
        // Does not URL encode any inputs yet
        if (is_array($this->auth_options)) {
             $url=cmis_repositorywrapper::getOpUrl($url,$this->auth_options);
        }
        
        $aUrl = parse_url($url);

        if ($this->_locationHost) $aUrl['host'] = $this->_locationHost;
        if ($this->_locationPort) $aUrl['port'] = $this->_locationPort;

        $url = $aUrl['scheme'] . '://' ;
        $url .= $aUrl['host'] ;
        if (isset($aUrl['port'])) $url .= ':' . $aUrl['port'];
        $url .= $aUrl['path'];
        if (isset($aUrl['query'])) $url .= '?' . $aUrl['query'];
        // TO-DO << fragments?

        $session = curl_init($url);

        curl_setopt($session,CURLOPT_HEADER,false);
        curl_setopt($session,CURLOPT_RETURNTRANSFER,true);
        if ($this->username) {
            curl_setopt($session,CURLOPT_USERPWD,$this->username . ":" . $this->password);
        }
        curl_setopt($session,CURLOPT_CUSTOMREQUEST,$method);
        if ($contentType) {
            $headers=array();
            $headers["Content-Type"]=$contentType;
            curl_setopt($session,CURLOPT_HTTPHEADER, $headers);
        }
        if ($content) {
            curl_setopt($session,CURLOPT_POSTFIELDS, $content);
        }
        if ($method == "POST") {
              curl_setopt($session, CURLOPT_HTTPHEADER, array("Content-Type: " . $contentType));
              curl_setopt($session,CURLOPT_POST,true);
        }
        //TODO: Make this storage optional
        $retval = new stdClass();
        $retval->url=$url;
        $retval->method=$method;
        $retval->content_sent=$content;
        $retval->content_type_sent=$contentType;
        $retval->body=curl_exec($session);
        $retval->code = curl_getinfo($session,CURLINFO_HTTP_CODE);
        $retval->content_type=curl_getinfo($session,CURLINFO_CONTENT_TYPE);
        $retval->content_length=curl_getinfo($session,CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        curl_close($session);
        $this->last_request=$retval;
        return $retval;
    }

    function getLastRequest() {
        return $this->last_request;
    }

    function getLastRequestBody() {
        return $this->last_request->body;
    }

    function getLastRequestCode() {
        return $this->last_request->code;
    }

    function getLastRequestContentType() {
        return $this->last_request->content_type;
    }

    function getLastRequestContentLength() {
        return $this->last_request->content_length;
    }

    function getLastRequestURL() {
        return $this->last_request->url;
    }

    function getLastRequestMethod() {
        return $this->last_request->method;
    }

    function getLastRequestContentTypeSent() {
        return $this->last_request->content_type_sent;
    }

    function getLastRequestContentSent() {
        return $this->last_request->content_sent;
    }

    // Static Utility Functions
    static function processTemplate($template,$values=array()) {
        // Fill in the blanks --
        $retval=$template;
        if (is_array($values)) {
            foreach ($values as $name => $value) {
                $retval = str_replace("{" . $name . "}",$value,$retval);
            }
        }
        // Fill in any unpoupated variables with ""
        return preg_replace("/{[a-zA-Z0-9_]+}/","",$retval);

    }

    static function doXQuery($xmldata,$xquery) {
        $doc=new DOMDocument();
        $doc->loadXML($xmldata);
        return cmis_repositorywrapper::doXQueryFromNode($doc,$xquery);
    }

    static function doXQueryFromNode($xmlnode,$xquery) {
        // Perform an XQUERY on a NODE
        // Register the 4 CMIS namespaces
        $xpath=new DomXPath($xmlnode);
        foreach (cmis_repositorywrapper::$namespaces as $nspre => $nsuri) {
            $xpath->registerNamespace($nspre,$nsuri);
        }
        return $xpath->query($xquery);

    }
    static function getLinksArray($xmlnode) {
        // Gets the links of an object or a workspace
        // Distinguishes between the two "down" links
        //  -- the children link is put into the associative array with the "down" index
        //  -- the descendants link is put into the associative array with the "down-tree" index
        //  These links are distinquished by the mime type attribute, but these are probably the only two links that share the same rel ..
        //    so this was done as a one off
        $links = array();
        $link_nodes = $xmlnode->getElementsByTagName("link");
        foreach ($link_nodes as $ln) {
            if ($ln->attributes->getNamedItem("rel")->nodeValue == "down" && $ln->attributes->getNamedItem("type")->nodeValue == "application/cmistree+xml") {
                //Descendents and Childredn share same "rel" but different document type
                $links["down-tree"] = $ln->attributes->getNamedItem("href")->nodeValue;
            } else {
                $links[$ln->attributes->getNamedItem("rel")->nodeValue] = $ln->attributes->getNamedItem("href")->nodeValue;
            }
        }
        return $links;
    }
    static function extractObject($xmldata) {
        $doc=new DOMDocument();
        $doc->loadXML($xmldata);
        return cmis_repositorywrapper::extractObjectFromNode($doc);
    }
    static function extractObjectFromNode($xmlnode) {
        // Extracts the contents of an Object and organizes them into:
        //  -- Links
        //  -- Properties
        //  -- the Object ID
        // RRM -- NEED TO ADD ALLOWABLEACTIONS
        $retval = new stdClass();
        $retval->links=cmis_repositorywrapper::getLinksArray($xmlnode);
        $retval->properties=array();

        if (!method_exists($xmlnode->getElementsByTagName("object")->item(0),"getElementsByTagName")) {
            return $retval;
        }
        $prop_nodes = $xmlnode->getElementsByTagName("object")->item(0)->getElementsByTagName("properties")->item(0)->childNodes;
        foreach ($prop_nodes as $pn) {
            if ($pn->attributes) {
                $retval->properties[$pn->attributes->getNamedItem("propertyDefinitionId")->nodeValue] = $pn->getElementsByTagName("value")->item(0)->nodeValue;
            }
        }
        $retval->uuid=$xmlnode->getElementsByTagName("id")->item(0)->nodeValue;
        $retval->id=$retval->properties["cmis:objectId"];
        return $retval;
    }

    static function extractTypeDef($xmldata) {
        $doc=new DOMDocument();
        $doc->loadXML($xmldata);
        return cmis_repositorywrapper::extractTypeDefFromNode($doc);

    }
    static function extractTypeDefFromNode($xmlnode) {
        // Extracts the contents of an Object and organizes them into:
        //  -- Links
        //  -- Properties
        //  -- the Object ID
        // RRM -- NEED TO ADD ALLOWABLEACTIONS
        $retval = new stdClass();
        $retval->links=cmis_repositorywrapper::getLinksArray($xmlnode);
        $retval->properties=array();
        $retval->attributes=array();
        $result = cmis_repositorywrapper::doXQueryFromNode($xmlnode,"//cmisra:type/*");
        foreach ($result as $node) {
            if ((substr($node->nodeName,0,13) == "cmis:property")  && (substr($node->nodeName,-10) == "Definition")){
                $id=$node->getElementsByTagName("id")->item(0)->nodeValue;
                $cardinality=$node->getElementsByTagName("cardinality")->item(0)->nodeValue;
                $propertyType=$node->getElementsByTagName("propertyType")->item(0)->nodeValue;
                // Stop Gap for now
                $retval->properties[$id]= array(
                        "cmis:propertyType" =>$propertyType,
                        "cmis:cardinality" =>$cardinality,
                );
            } else {
                $retval->attributes[$node->nodeName]= $node->nodeValue;
            }
            $retval->id=$retval->attributes["cmis:id"];
        }

/*
 *



        $prop_nodes = $xmlnode->getElementsByTagName("object")->item(0)->getElementsByTagName("properties")->item(0)->childNodes;
        foreach ($prop_nodes as $pn) {
            if ($pn->attributes) {
                $retval->properties[$pn->attributes->getNamedItem("propertyDefinitionId")->nodeValue] = $pn->getElementsByTagName("value")->item(0)->nodeValue;
            }
        }
        $retval->uuid=$xmlnode->getElementsByTagName("id")->item(0)->nodeValue;
        $retval->id=$retval->properties["cmis:objectId"];
 */
        return $retval;
    }

    static function extractObjectFeed($xmldata) {
        //Assumes only one workspace for now
        $doc=new DOMDocument();

        $doc->loadXML($xmldata);
        return cmis_repositorywrapper::extractObjectFeedFromNode($doc);
    }
    static function extractObjectFeedFromNode($xmlnode) {
        // Process a feed and extract the objects
        //   Does not handle hierarchy
        //   Provides two arrays
        //   -- one sequential array (a list)
        //   -- one hash table indexed by objectID
        $retval = new stdClass();
        $retval->objectList=array();
        $retval->objectsById=array();
        $result = cmis_repositorywrapper::doXQueryFromNode($xmlnode,"//atom:entry");
        foreach ($result as $node) {
            $obj = cmis_repositorywrapper::extractObjectFromNode($node);
            $retval->objectsById[$obj->id]=$obj;
            $retval->objectList[]=&$retval->objectsById[$obj->id];
        }
        return $retval;
    }

    static function extractWorkspace($xmldata) {
        //Assumes only one workspace for now
        $doc=new DOMDocument();
        $doc->loadXML($xmldata);
        return cmis_repositorywrapper::extractWorkspaceFromNode($doc);
    }
    static function extractWorkspaceFromNode($xmlnode) {
        // Assumes only one workspace for now
        // Load up the workspace object with arrays of
        //  links
        //  URI Templates
        //  Collections
        //  Capabilities
        //  General Repository Information
        $retval = new stdClass();
        $retval->links=cmis_repositorywrapper::getLinksArray($xmlnode);
        $retval->uritemplates=array();
        $retval->collections=array();
        $retval->capabilities=array();
        $retval->repositoryInfo=array();
        $result= cmis_repositorywrapper::doXQueryFromNode($xmlnode,"//cmisra:uritemplate");
        foreach ($result as $node) {
            $retval->uritemplates[$node->getElementsByTagName("type")->item(0)->nodeValue] =
                $node->getElementsByTagName("template")->item(0)->nodeValue;
        }
        $result= cmis_repositorywrapper::doXQueryFromNode($xmlnode,"//app:collection");
        foreach ($result as $node) {
            $retval->collections[$node->getElementsByTagName("collectionType")->item(0)->nodeValue] =
                $node->attributes->getNamedItem("href")->nodeValue;
        }
        $result = cmis_repositorywrapper::doXQueryFromNode($xmlnode,"//cmis:capabilities/*");
        foreach ($result as $node) {
            $retval->capabilities[$node->nodeName]= $node->nodeValue;
        }
        $result = cmis_repositorywrapper::doXQueryFromNode($xmlnode,"//cmisra:repositoryInfo/*");
        foreach ($result as $node) {
            if ($node->nodeName != "cmis:capabilities") {
                $retval->repositoryInfo[$node->nodeName]= $node->nodeValue;
            }
        }

        return $retval;
    }
}
